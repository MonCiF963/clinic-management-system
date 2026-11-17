<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use App\Support\Slots;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** A doctor books (already confirmed) appointments for THEIR OWN patients. */
class BookingController extends Controller
{
    private function doctor(Request $request): Doctor
    {
        return Doctor::where('user_id', $request->user()->id)->firstOrFail();
    }

    /** Patients who have (or had) an appointment with this doctor. */
    private function myPatients(Doctor $doctor)
    {
        return Patient::with('user')
            ->whereIn('id', Appointment::where('doctor_id', $doctor->id)->select('patient_id'))
            ->get()->sortBy(fn ($p) => $p->user->name)->values();
    }

    public function create(Request $request): View
    {
        $doctor = $this->doctor($request);
        $patients = $this->myPatients($doctor);
        $services = Service::active()->orderBy('name')->get();

        $slots = collect();
        $chosen = null;
        $date = $request->query('date');

        if ($request->query('patient_id') && $request->query('service_id') && $date
            && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)
            && $date >= now(Slots::tz())->toDateString()
            && $date <= now(Slots::tz())->addDays((int) config('clinic.booking_window_days'))->toDateString()) {
            $patient = $patients->firstWhere('id', (int) $request->query('patient_id'));
            $service = $services->firstWhere('id', (int) $request->query('service_id'));

            if ($patient && $service) {
                $chosen = compact('patient', 'service', 'date');
                $slots = Slots::for($doctor, $service, $date);
            }
        }

        return view('doctor.create', compact('doctor', 'patients', 'services', 'slots', 'chosen') + [
            'today' => now(Slots::tz())->toDateString(),
            'maxDate' => now(Slots::tz())->addDays((int) config('clinic.booking_window_days'))->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $doctor = $this->doctor($request);
        $patients = $this->myPatients($doctor);

        $data = $request->validate([
            'patient_id' => ['required', 'integer', Rule::in($patients->pluck('id')->all())],
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')->where('is_active', true)],
            'start' => ['required', 'date_format:Y-m-d H:i'],
        ]);

        $service = Service::findOrFail($data['service_id']);
        $start = Carbon::createFromFormat('Y-m-d H:i', $data['start'], Slots::tz());

        // Only accept times that are really free (never trust the form).
        if (! Slots::for($doctor, $service, $start->toDateString())->contains(fn ($s) => $s->equalTo($start))) {
            return back()->with('error', 'That time is no longer available. Please pick another one.');
        }

        try {
            $appointment = Appointment::create([
                'patient_id' => $data['patient_id'],
                'doctor_id' => $doctor->id,
                'service_id' => $service->id,
                'start_at' => $start->copy()->utc(),
                'end_at' => $start->copy()->addMinutes($service->duration_minutes)->utc(),
                'status' => 'confirmed', // booked by the doctor, so no extra confirmation needed
                'created_by' => $request->user()->id,
            ]);
        } catch (QueryException $e) {
            if (in_array($e->getCode(), ['23P01', '23505'], true)) {
                return back()->with('error', 'That time was just taken. Please pick another one.');
            }
            throw $e;
        }

        AuditLog::record('appointment.create_by_doctor', $appointment);

        return redirect()->route('appointments.index')->with('status', 'Appointment booked and confirmed.');
    }
}

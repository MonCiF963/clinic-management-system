<?php

namespace App\Http\Controllers;

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

class AppointmentController extends Controller
{
    /** What each status can become next. */
    public const NEXT = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['checked_in', 'cancelled', 'no_show'],
        'checked_in' => ['completed'],
    ];

    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Appointment::query()
            ->with(['patient.user', 'doctor.user', 'service'])
            ->orderByDesc('start_at');

        if ($user->hasRole('patient')) {
            $query->whereIn('patient_id', Patient::where('user_id', $user->id)->select('id'));
        } elseif ($user->hasRole('doctor')) {
            $query->whereIn('doctor_id', Doctor::where('user_id', $user->id)->select('id'));
        }

        if ($status = $request->query('status')) {
            if (in_array($status, Appointment::STATUSES, true)) {
                $query->where('status', $status);
            }
        }

        return view('appointments.index', [
            'appointments' => $query->paginate(15)->withQueryString(),
            'statuses' => Appointment::STATUSES,
            'next' => self::NEXT,
        ]);
    }

    public function create(Request $request): View
    {
        $services = Service::active()->orderBy('name')->get();
        $doctors = Doctor::with(['user', 'specialty'])->get()->sortBy(fn ($d) => $d->user->name)->values();

        $slots = collect();
        $chosen = null;

        $serviceId = $request->query('service_id');
        $doctorId = $request->query('doctor_id');
        $date = $request->query('date');

        if ($serviceId && $doctorId && $date) {
            $max = now(Slots::tz())->addDays((int) config('clinic.booking_window_days'))->toDateString();

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && $date >= now(Slots::tz())->toDateString() && $date <= $max) {
                $service = $services->firstWhere('id', (int) $serviceId);
                $doctor = $doctors->firstWhere('id', (int) $doctorId);

                if ($service && $doctor) {
                    $chosen = ['service' => $service, 'doctor' => $doctor, 'date' => $date];
                    $slots = Slots::for($doctor, $service, $date);
                }
            }
        }

        return view('appointments.create', compact('services', 'doctors', 'slots', 'chosen') + [
            'today' => now(Slots::tz())->toDateString(),
            'maxDate' => now(Slots::tz())->addDays((int) config('clinic.booking_window_days'))->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')->where('is_active', true)],
            'doctor_id' => ['required', 'integer', Rule::exists('doctors', 'id')->whereNull('deleted_at')],
            'start' => ['required', 'date_format:Y-m-d H:i'],
        ]);

        $patient = Patient::firstOrCreate(['user_id' => $user->id]);

        $active = Appointment::where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_at', '>', now())
            ->count();

        if ($active >= (int) config('clinic.max_active_per_patient')) {
            return back()->with('error', 'You already have the maximum number of upcoming appointments. Cancel one first.');
        }

        $service = Service::findOrFail($data['service_id']);
        $doctor = Doctor::findOrFail($data['doctor_id']);
        $start = Carbon::createFromFormat('Y-m-d H:i', $data['start'], Slots::tz());

        // Only accept times that are really free (never trust the form).
        $free = Slots::for($doctor, $service, $start->toDateString())
            ->contains(fn ($s) => $s->equalTo($start));

        if (! $free) {
            return back()->with('error', 'Sorry, that time is no longer available. Please pick another one.');
        }

        try {
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'service_id' => $service->id,
                'start_at' => $start->copy()->utc(),
                'end_at' => $start->copy()->addMinutes($service->duration_minutes)->utc(),
                'status' => 'pending',
                'created_by' => $user->id,
            ]);
        } catch (QueryException $e) {
            // The database refuses overlapping bookings for the same doctor.
            if (in_array($e->getCode(), ['23P01', '23505'], true)) {
                return back()->with('error', 'Someone just booked that time. Please pick another one.');
            }
            throw $e;
        }

        AuditLog::record('appointment.create', $appointment);

        return redirect()->route('appointments.index')->with('status', 'Appointment requested. The clinic will confirm it shortly.');
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $user = $request->user();

        $mine = $user->hasRole('patient')
            && $appointment->patient_id === Patient::where('user_id', $user->id)->value('id');

        abort_unless($mine || $user->hasRole('admin', 'receptionist'), 403);

        if (! in_array($appointment->status, ['pending', 'confirmed'], true) || $appointment->start_at->isPast()) {
            return back()->with('error', 'This appointment can no longer be cancelled.');
        }

        $appointment->update(['status' => 'cancelled']);
        AuditLog::record('appointment.cancel', $appointment);

        return back()->with('status', 'Appointment cancelled.');
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasRole('doctor')) {
            abort_unless($appointment->doctor_id === Doctor::where('user_id', $user->id)->value('id'), 403);
        }

        $status = $request->validate(['status' => ['required', Rule::in(Appointment::STATUSES)]])['status'];

        if (! in_array($status, self::NEXT[$appointment->status] ?? [], true)) {
            return back()->with('error', 'That status change is not allowed.');
        }

        if ($user->hasRole('doctor') && $status !== 'completed') {
            abort(403);
        }

        $appointment->update(['status' => $status]);
        AuditLog::record('appointment.'.$status, $appointment);

        return back()->with('status', 'Appointment updated.');
    }
}

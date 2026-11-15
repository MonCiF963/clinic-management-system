<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\AuditLog;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public const DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    public function index(): View
    {
        return view('admin.doctors.index', [
            'doctors' => Doctor::with(['user', 'specialty'])->withCount('availabilities')->get()->sortBy(fn ($d) => $d->user->name)->values(),
            'specialties' => Specialty::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'specialty_id' => ['required', 'integer', 'exists:specialties,id'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'languages' => ['nullable', 'string', 'max:100'],
        ]);

        if (User::withTrashed()->whereRaw('lower(email) = ?', [$data['email']])->exists()) {
            return back()->withInput($request->except('password'))->withErrors(['email' => 'This email is already used.']);
        }

        try {
            $doctor = DB::transaction(function () use ($data) {
                $user = new User;
                $user->forceFill([
                    'name' => trim($data['name']),
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => $data['password'],
                    'role' => 'doctor',
                    'locale' => 'en',
                ])->save();

                return Doctor::create([
                    'user_id' => $user->id,
                    'specialty_id' => $data['specialty_id'],
                    'bio' => $data['bio'] ?? null,
                    'languages' => $data['languages'] ?? null,
                ]);
            });
        } catch (UniqueConstraintViolationException) {
            return back()->withInput($request->except('password'))->withErrors(['email' => 'This email is already used.']);
        }

        AuditLog::record('doctor.create', $doctor);

        return redirect()->route('admin.doctors.show', $doctor)->with('status', 'Doctor added. Now set the weekly working hours.');
    }

    public function show(Doctor $doctor): View
    {
        $doctor->load(['user', 'specialty']);

        return view('admin.doctors.show', [
            'doctor' => $doctor,
            'hours' => $doctor->availabilities()->orderBy('weekday')->orderBy('start_time')->get()->groupBy('weekday'),
            'days' => self::DAYS,
        ]);
    }

    public function addHours(Request $request, Doctor $doctor): RedirectResponse
    {
        $data = $request->validate([
            'weekday' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $overlap = $doctor->availabilities()
            ->where('weekday', $data['weekday'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();

        if ($overlap) {
            return back()->withInput()->with('error', 'These hours overlap with hours already set for that day.');
        }

        try {
            $doctor->availabilities()->create($data);
        } catch (UniqueConstraintViolationException) {
            return back()->with('error', 'These hours already exist.');
        }

        return back()->with('status', 'Working hours added.');
    }

    public function removeHours(Doctor $doctor, Availability $availability): RedirectResponse
    {
        abort_unless($availability->doctor_id === $doctor->id, 404);
        $availability->delete();

        return back()->with('status', 'Working hours removed.');
    }
}

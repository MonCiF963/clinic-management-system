<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Patients list for staff. Doctors only see patients who have booked with them. */
class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $q = trim((string) $request->query('q', ''));

        $patients = Patient::query()
            ->with('user')
            ->withCount('appointments')
            ->withMax('appointments', 'start_at')
            ->when($user->hasRole('doctor'), function ($query) use ($user) {
                $doctorId = Doctor::where('user_id', $user->id)->value('id') ?? 0;
                $query->whereIn('id', Appointment::where('doctor_id', $doctorId)->select('patient_id'));
            })
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], mb_strtolower($q)).'%';
                $query->whereHas('user', fn ($u) => $u
                    ->whereRaw('lower(name) like ?', [$like])
                    ->orWhereRaw('lower(email) like ?', [$like])
                    ->orWhereRaw("lower(coalesce(phone, '')) like ?", [$like]));
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('patients.index', compact('patients', 'q'));
    }
}

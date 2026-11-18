<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Read-only user management: search, filter by role. */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $roles = ['admin', 'doctor', 'receptionist', 'patient'];
        $q = trim((string) $request->query('q', ''));
        $role = $request->query('role');

        $users = User::query()
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], mb_strtolower($q)).'%';
                $w->whereRaw('lower(name) like ?', [$like])
                  ->orWhereRaw('lower(email) like ?', [$like])
                  ->orWhereRaw('lower(coalesce(phone, \'\')) like ?', [$like]);
            }))
            ->when(in_array($role, $roles, true), fn ($query) => $query->where('role', $role))
            ->with(['patient' => fn ($p) => $p->withCount('appointments'), 'doctor.specialty'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $counts = User::query()->selectRaw('role, count(*)::int as c')->groupBy('role')->pluck('c', 'role');

        return view('admin.users.index', compact('users', 'roles', 'counts', 'q', 'role'));
    }

    /** One account: contact details, role info and recent appointments. */
    public function show(User $user): View
    {
        $user->load(['patient', 'doctor.specialty']);

        $appointments = collect();
        if ($user->patient || $user->doctor) {
            $appointments = Appointment::query()
                ->with(['patient.user', 'doctor.user', 'service'])
                ->when($user->patient, fn ($q) => $q->where('patient_id', $user->patient->id))
                ->when(! $user->patient && $user->doctor, fn ($q) => $q->where('doctor_id', $user->doctor->id))
                ->orderByDesc('start_at')
                ->limit(10)
                ->get();
        }

        return view('admin.users.show', compact('user', 'appointments'));
    }
}

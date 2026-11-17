<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $counts = User::query()->selectRaw('role, count(*)::int as c')->groupBy('role')->pluck('c', 'role');

        return view('admin.users.index', compact('users', 'roles', 'counts', 'q', 'role'));
    }
}

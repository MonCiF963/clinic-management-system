<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Bots fill the hidden field. Pretend it worked.
        if ($request->filled('website')) {
            return redirect()->route('home');
        }

        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120', "regex:/^[\\p{L}\\p{M}][\\p{L}\\p{M}\\s'.\\-]+$/u"],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\\+?[0-9][0-9\\s\\-]{6,}$/'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'name.regex' => 'Please use letters only in your name.',
            'phone.regex' => 'Please enter a valid phone number.',
        ]);

        // The database enforces unique emails (case-insensitive, including closed accounts).
        if (User::withTrashed()->whereRaw('lower(email) = ?', [$data['email']])->exists()) {
            return back()->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'An account with this email already exists. Try signing in.']);
        }

        try {
            $user = DB::transaction(function () use ($data) {
                $user = new User;
                $user->forceFill([
                    'name' => trim($data['name']),
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => $data['password'], // hashed by the model cast
                    'role' => 'patient',              // never taken from the request
                    'locale' => 'en',
                ])->save();

                Patient::create(['user_id' => $user->id]);

                return $user;
            });
        } catch (UniqueConstraintViolationException) {
            return back()->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'An account with this email already exists. Try signing in.']);
        }

        Auth::login($user);
        $request->session()->regenerate();
        AuditLog::record('register', $user);

        return redirect()->route('appointments.create')->with('status', 'Welcome! Your account is ready. Pick a time for your appointment.');
    }
}

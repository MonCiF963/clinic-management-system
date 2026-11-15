<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class EnsureAdmin extends Command
{
    protected $signature = 'clinic:ensure-admin';

    protected $description = 'Create the first admin from ADMIN_EMAIL / ADMIN_PASSWORD if it does not exist yet';

    public function handle(): int
    {
        $email = mb_strtolower(trim((string) config('clinic.admin.email')));
        $password = (string) config('clinic.admin.password');

        if ($email === '' || $password === '') {
            $this->line('ADMIN_EMAIL / ADMIN_PASSWORD not set: skipping.');

            return self::SUCCESS;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 10) {
            $this->error('ADMIN_EMAIL must be valid and ADMIN_PASSWORD at least 10 characters.');

            return self::FAILURE;
        }

        $user = User::withTrashed()->whereRaw('lower(email) = ?', [$email])->first();

        if ($user) {
            if ($user->role !== 'admin' || $user->trashed()) {
                $user->forceFill(['role' => 'admin', 'deleted_at' => null])->save();
                $this->info("Existing account {$email} is now an admin.");
            } else {
                $this->line('Admin already exists.');
            }

            return self::SUCCESS;
        }

        $user = new User;
        $user->forceFill([
            'name' => config('clinic.admin.name') ?: 'Administrator',
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
            'locale' => 'en',
            'email_verified_at' => now(),
        ])->save();

        $this->info("Admin {$email} created.");

        return self::SUCCESS;
    }
}

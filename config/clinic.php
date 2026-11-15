<?php

return [
    // Opening hours and appointment times are entered and shown in this timezone.
    // The database stores UTC; the app timezone stays UTC.
    'timezone' => env('CLINIC_TIMEZONE', 'Africa/Algiers'),

    'currency' => env('CLINIC_CURRENCY', 'DA'),

    // Patients cannot book less than this many minutes before the slot.
    'min_notice_minutes' => 60,

    // How many days ahead a patient can book.
    'booking_window_days' => 60,

    // Max upcoming active appointments per patient (anti-abuse).
    'max_active_per_patient' => 5,

    // Optional first admin, created at startup if missing (set in Render, never commit).
    'admin' => [
        'name' => env('ADMIN_NAME', 'Administrator'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],
];

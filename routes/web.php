<?php

use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SpecialtyController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Appointments: patients book and cancel their own, staff manage everything.
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');

    Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/appointments/create', [\App\Http\Controllers\Doctor\BookingController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [\App\Http\Controllers\Doctor\BookingController::class, 'store'])->middleware('throttle:20,1')->name('appointments.store');
    });

    Route::middleware('role:patient')->group(function () {
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [AppointmentController::class, 'store'])->middleware('throttle:10,1')->name('appointments.store');
    });

    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
        ->middleware('role:patient,admin,receptionist')->name('appointments.cancel');
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
        ->middleware('role:admin,receptionist,doctor')->name('appointments.status');

    // Admin only.
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])
        ->middleware('role:admin')->name('appointments.destroy');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');

        Route::get('/specialties', [SpecialtyController::class, 'index'])->name('specialties.index');
        Route::post('/specialties', [SpecialtyController::class, 'store'])->name('specialties.store');
        Route::delete('/specialties/{specialty}', [SpecialtyController::class, 'destroy'])->name('specialties.destroy');

        Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
        Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
        Route::get('/doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');
        Route::post('/doctors/{doctor}/hours', [DoctorController::class, 'addHours'])->name('doctors.hours.add');
        Route::delete('/doctors/{doctor}/hours/{availability}', [DoctorController::class, 'removeHours'])->name('doctors.hours.remove');
    });
});

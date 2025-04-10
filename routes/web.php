<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;


Route::get('/users', function () {
    return \App\Models\User::all();
})->name('users');

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
    Route::post('/appointments/store', [AppointmentController::class, 'store']);
    Route::delete('/appointments/delete/{id}', [AppointmentController::class, 'delete']);
    Route::get('/appointments/today', [AppointmentController::class, 'getTodayAppointments'])->name('appointments.today');
});


require __DIR__.'/auth.php';

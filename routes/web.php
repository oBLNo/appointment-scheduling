<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;


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

require __DIR__.'/auth.php';

Route::get('/appointments', [AppointmentController::class, 'index'])->middleware('auth')->name('appointments'); // Zeigt den Kalender
Route::post('/appointments/store', [AppointmentController::class, 'store']); // Speichert Termine
Route::get('/appointments/data', [AppointmentController::class, 'fetch']); // Holt alle Termine für den Kalender


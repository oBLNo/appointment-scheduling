<?php


use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;
use App\Models\User;



Route::middleware('auth')->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
    Route::post('/appointments/store', [AppointmentController::class, 'store']);
    Route::delete('/appointments/delete/{id}', [AppointmentController::class, 'delete']);
    Route::get('/appointments/today', [AppointmentController::class, 'getTodayAppointments'])->name('appointments.today');
});

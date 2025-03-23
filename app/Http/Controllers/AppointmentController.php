<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function index()
    {
        return view('appointments.index');
    }

    public function fetch()
    {
        return response()->json(Appointment::all());
    }
    public function getTodayAppointments()
    {
        $today = now()->toDateString(); // Today's date in format "YYYY-MM-DD"

        $appointments = Appointment::whereDate('start', $today)->get();

        return response()->json($appointments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'nullable|date',
        ]);

        try {
            Appointment::create([
                'title' => $request->title,
                'start' => $request->start,
                'end' => $request->end ?? $request->start, // Falls kein Enddatum angegeben wird
            ]);

            return response()->json(['message' => 'Appointment saved successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to save appointment.', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        $appointment = Appointment::find($id);
        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found.'], 404);
        }
        try {
            $appointment->delete();
            return response()->json(['message' => 'Appointment deleted successfully.']);
        }catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete appointment.', 'error' => $e->getMessage()], 500);
        }
    }


}

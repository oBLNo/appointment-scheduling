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

    public function getAppointments()
    {
        $appointments = Appointment::with('assignedUser:id,name')
            ->select('id', 'title', 'start', 'end', 'assigned_to')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->title,
                    'start' => $appointment->start,
                    'end' => $appointment->end,
                    'assigned_to' => $appointment->assigned_to,
                    'assigned_user' => $appointment->assignedUser ? ['name' => $appointment->assignedUser->name] : null,
                ];
            });

        return response()->json($appointments);
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
            'assigned_to' => 'required|integer',
        ]);
        try {
            Appointment::create([
                'title' => $request->title,
                'start' => $request->start,
                'end' => $request->end ?? $request->start,
                'assigned_to' => $request->assigned_to,
            ]);

            return response()->json(['message' => 'Appointment saved successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to save appointment.', 'error' => $e->getMessage()], 500, ['Content-Type' => 'application/json']);
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
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete appointment.', 'error' => $e->getMessage()], 500);
        }
    }


}

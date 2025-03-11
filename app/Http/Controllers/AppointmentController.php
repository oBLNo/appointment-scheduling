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

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'nullable|date',
        ]);

        Appointment::create([
            'title' => $request->title,
            'start' => $request->start,
            'end' => $request->end ?? $request->start, // Falls kein Enddatum angegeben wird
        ]);

        return response()->json(['message' => 'Appointment saved successfully.']);
    }
}

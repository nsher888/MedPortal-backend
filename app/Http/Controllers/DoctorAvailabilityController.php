<?php

namespace App\Http\Controllers;

use App\Models\DoctorAvailability;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorAvailabilityController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $existingAvailability = DoctorAvailability::where('doctor_id', auth()->id())
            ->where('date', $request->date)
            ->first();

        if ($existingAvailability) {
            $existingAvailability->update([
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ]);
            $this->regenerateTimeSlots($existingAvailability);
            $availability = $existingAvailability;
        } else {
            $availability = DoctorAvailability::create([
                'doctor_id' => auth()->id(),
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ]);
            $this->generateTimeSlots($availability);
        }

        return response()->json($availability, 201);
    }

    public function storeMultiple(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $existingAvailability = DoctorAvailability::where('doctor_id', auth()->id())
                ->where('date', $date->toDateString())
                ->first();

            if ($existingAvailability) {
                $existingAvailability->update([
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                ]);
                $this->regenerateTimeSlots($existingAvailability);
            } else {
                $availability = DoctorAvailability::create([
                    'doctor_id' => auth()->id(),
                    'date' => $date->toDateString(),
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                ]);
                $this->generateTimeSlots($availability);
            }
        }

        return response()->json(['message' => 'Availabilities set successfully'], 201);
    }

    private function generateTimeSlots(DoctorAvailability $availability)
    {
        $startTime = Carbon::parse($availability->start_time)->setTimezone('UTC');
        $endTime = Carbon::parse($availability->end_time)->setTimezone('UTC');

        while ($startTime < $endTime) {
            TimeSlot::create([
                'doctor_id' => $availability->doctor_id,
                'date' => $availability->date,
                'start_time' => $startTime->format('H:i:s'),
                'status' => 'free',
            ]);

            $startTime->addMinutes(30);
        }
    }

    private function regenerateTimeSlots(DoctorAvailability $availability)
    {
        TimeSlot::where('doctor_id', $availability->doctor_id)
            ->where('date', $availability->date)
            ->delete();

        $this->generateTimeSlots($availability);
    }

    public function index()
    {
        $availabilities = DoctorAvailability::where('doctor_id', auth()->id())->get();
        return response()->json($availabilities);
    }

    public function getAvailableTimeSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $timeSlots = TimeSlot::where('doctor_id', $request->doctor_id)
            ->where('date', $request->date)
            ->where('status', 'free')
            ->get();

        return response()->json($timeSlots);
    }

    public function getDoctorTimeSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $doctor_id = auth()->user()->id;

        $timeSlots = TimeSlot::where('doctor_id', $doctor_id)
            ->where('date', $request->date)
            ->leftJoin('users', 'time_slots.patient_id', '=', 'users.id')
            ->select('time_slots.*', 'users.name as patient_name', 'users.surname as patient_surname')
            ->get();

        return response()->json($timeSlots);
    }

    public function cancelTimeSlot($id)
    {
        $timeSlot = TimeSlot::find($id);

        if (!$timeSlot) {
            return response()->json(['message' => 'Time slot not found'], 404);
        }

        $timeSlot->delete();

        return response()->json(['message' => 'Time slot canceled successfully']);
    }

    public function makeTimeSlotUnAvailable($id)
    {
        $timeSlot = TimeSlot::find($id);

        if (!$timeSlot) {
            return response()->json(['message' => 'Time slot not found'], 404);
        }

        $timeSlot->update(['status' => 'unavailable']);

        return response()->json(['message' => 'Time slot is now unavailable']);
    }

    public function changeTimeSlotStatus($id)
    {
        $timeSlot = TimeSlot::find($id);

        if (!$timeSlot) {
            return response()->json(['message' => 'Time slot not found'], 404);
        }

        if ($timeSlot->status == 'booked') {
            return response()->json(['message' => 'Time slot is booked and cannot be changed'], 400);
        }

        $newStatus = $timeSlot->status == 'free' ? 'unavailable' : 'free';
        $timeSlot->update(['status' => $newStatus]);

        return response()->json(['message' => "Time slot is now $newStatus"]);
    }


    public function makeTimeSotAvailable($id)
    {
        $timeSlot = TimeSlot::find($id);

        if (!$timeSlot) {
            return response()->json(['message' => 'Time slot not found'], 404);
        }

        $timeSlot->update(['status' => 'free']);

        return response()->json(['message' => 'Time slot is now available']);
    }

    public function getAvailableDates($id)
    {
        $dates = DoctorAvailability::where('doctor_id', $id)
            ->where('date', '>=', now()->toDateString())
            ->get()
            ->filter(function ($availability) {
                return TimeSlot::where('doctor_id', $availability->doctor_id)
                    ->where('date', $availability->date)
                    ->where('status', 'free')
                    ->exists();
            })
            ->pluck('date');

        return response()->json($dates);
    }

    public function cancelAvailability($id)
    {
        $availability = DoctorAvailability::find($id);

        if (!$availability) {
            return response()->json(['message' => 'Availability not found'], 404);
        }

        TimeSlot::where('doctor_id', $availability->doctor_id)
            ->where('date', $availability->date)
            ->delete();

        $availability->delete();

        return response()->json(['message' => 'Availability canceled successfully']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Events\AppointmentBooked;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function book(Request $request)
    {
        $request->validate([
            'time_slot_id' => 'required|exists:time_slots,id',
        ]);

        Log::info('Booking appointment for user: ' . auth()->id());

        $timeSlot = TimeSlot::find($request->time_slot_id);

        if ($timeSlot->status != 'free') {
            return response()->json(['message' => 'Time slot is already booked'], 400);
        }

        Log::info('Time slot is free, booking appointment');

        $timeSlot->update([
            'status' => 'booked',
            'patient_id' => auth()->id(),
        ]);

        Log::info('Appointment booked successfully');

        $receiver = User::find($request->doctor_id);
        $sender = User::find(auth()->id());

        $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i:s', "{$timeSlot->date} {$timeSlot->start_time}");
        $formattedDateTime = $appointmentDateTime->format('Y-m-d H:i:s');

        $patientName = "{$sender->name} {$sender->surname}";
        $message = "Appointment booked on {$formattedDateTime} with patient {$patientName}";

        // Create the notification in the database
        $notification = $receiver->notifications()->create([
            'message' => $message,
            'read' => false,
        ]);

        // Broadcast the event with the notification data
        broadcast(new AppointmentBooked($notification));

        return response()->json(['message' => 'Appointment booked successfully']);
    }




    public function cancelAppointment(Request $request, $id)
    {
        $timeSlot = TimeSlot::find($id);

        if (!$timeSlot) {
            return response()->json(['message' => 'Time slot not found'], 404);
        }

        if ($timeSlot->status == 'free') {
            return response()->json(['message' => 'Time slot is already free'], 400);
        }

        $timeSlot->update([
            'status' => 'free',
            'patient_id' => null,
        ]);

        return response()->json(['message' => 'Appointment canceled successfully']);
    }

    public function index()
    {
        $appointments = TimeSlot::where('patient_id', auth()->id())->get();

        $appointments = $appointments->map(function ($timeSlot) {
            return [
                'id' => $timeSlot->id,
                'date' => Carbon::parse($timeSlot->date)->setTimezone('Asia/Tbilisi')->format('Y-m-d'),
                'start_time' => Carbon::parse($timeSlot->start_time)->setTimezone('Asia/Tbilisi')->format('H:i'),
                'end_time' => Carbon::parse($timeSlot->end_time)->setTimezone('Asia/Tbilisi')->format('H:i'),
                'status' => $timeSlot->status,
                'doctor' => $timeSlot->doctor->name . ' ' . $timeSlot->doctor->surname,
            ];
        });

        return response()->json($appointments);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $clinic = auth()->user();
        $perPage = $request->input('per_page', 10);

        $doctors = User::role('doctor')->where('clinic_id', $clinic->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $doctors,
            'current_page' => $request->input('page', 1),
            'last_page' => $doctors->lastPage(),
            'total' => $doctors->total(),
        ]);
    }

    public function getAllDoctors()
    {
        $clinic = auth()->user();

        $doctors = User::role('doctor')->where('clinic_id', $clinic->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($doctors);
    }

    public function destroy($id)
    {
        $doctor = User::role('doctor')->find($id);
        $doctor->delete();
        return response()->json(['message' => 'Doctor deleted']);
    }

    public function show($id)
    {
        $doctor = User::role('doctor')->find($id);
        return response()->json($doctor);
    }

    public function updateDoctor(Request $request, $id)
    {
        $doctor = User::role('doctor')->find($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
        ]);

        $doctor->update([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
        ]);

        return response()->json([
            'message' => 'Doctor updated successfully',
            'doctor' => $doctor,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        try {
            DB::beginTransaction();

            $clinic = auth()->user();

            $doctor = User::create([
                'name' => $request->input('name'),
                'surname' => $request->input('surname'),
                'email' => $request->input('email'),
                'password' => Hash::make(Str::random(8)),
                'clinic_id' => $clinic->id,
            ]);

            $doctor->assignRole('doctor');

            DB::commit();

            $status = Password::sendResetLink(['email' => $doctor->email]);

            if ($status != Password::RESET_LINK_SENT) {
                throw ValidationException::withMessages([
                    'email' => [__($status)],
                ]);
            }

            return response()->json(['message' => 'Doctor created and invitation sent']);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating doctor: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}

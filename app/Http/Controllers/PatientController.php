<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user && $user->idNumber) {
            $query = Result::where('idNumber', $user->idNumber);

            // Apply date filtering
            if ($request->has('startDate') && $request->has('endDate')) {
                $startDate = $request->input('startDate');
                $endDate = $request->input('endDate');
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            // Apply sorting
            if ($request->has('sortOrder')) {
                $sortOrder = $request->input('sortOrder');
                $query->orderBy('created_at', $sortOrder);
            }

            $results = $query->get();

            $transformedResults = $results->transform(function ($result) {
                $doctorIds = json_decode($result->doctor_ids);
                $doctors = User::whereIn('id', $doctorIds)->get(['name', 'surname']);
                $clinic = User::find($result->clinic_id);

                return [
                    'id' => $result->id,
                    'patientName' => $result->patientName,
                    'surname' => $result->surname,
                    'dob' => $result->dob,
                    'idNumber' => $result->idNumber,
                    'testType' => Type::find($result->testType)->name,
                    'doctorNames' => $doctors->map(function ($doctor) {
                        return $doctor->name . ' ' . $doctor->surname;
                    })->toArray(),
                    'testResult' => Storage::disk('s3')->temporaryUrl(
                        $result->testResult,
                        now()->addMinutes(15)
                    ),
                    'created_at' => $result->created_at,
                    'updated_at' => $result->updated_at,
                    'clinicName' => $clinic ? $clinic->name : 'Unknown',
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $transformedResults,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated or idNumber not found',
            ], 401);
        }
    }

    public function getAllClinics()
    {
        $clinics = User::role('clinic')->get();
        return response()->json($clinics);
    }
}

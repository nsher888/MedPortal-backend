<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function getClinicStatistics()
    {
        $clinic = auth()->user();

        $doctorsNumber = User::role('doctor')->where('clinic_id', $clinic->id)->count();
        $resultsNumber = Result::where('clinic_id', $clinic->id)->count();
        $resultsData = Result::where('clinic_id', $clinic->id)->get();

        $resultsData = $resultsData->map(function ($result) {
            return [
                'id' => $result->id,
                'patientName' => $result->patientName,
                'surname' => $result->surname,
                'dob' => $result->dob,
                'idNumber' => $result->idNumber,
                'testType' => Type::find($result->testType)->name,
                'doctorNames' => User::whereIn('id', json_decode($result->doctor_ids))->pluck('name')->toArray(),
                'created_at' => $result->created_at,
                'updated_at' => $result->updated_at,
                'clinic_id' => $result->clinic_id,
            ];
        });

        $uniqueIds = $resultsData->pluck('idNumber')->unique()->count();

        return response()->json([
            'doctorsNumber' => $doctorsNumber,
            'resultsNumber' => $resultsNumber,
            'resultsData' => $resultsData,
            'uniqueIds' => $uniqueIds,
        ]);
    }
}

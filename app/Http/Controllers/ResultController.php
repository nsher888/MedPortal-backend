<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index()
    {
        $results = Result::with(['clinic', 'doctors', 'type'])->get();
        return response()->json($results);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'patient_identification_number' => 'required|string|max:255',
            'clinic_id' => 'required|exists:users,id',
            'type_id' => 'required|exists:types,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'file' => 'nullable|string',
            'doctor_ids' => 'nullable|array',
            'doctor_ids.*' => 'exists:users,id'
        ]);

        $result = Result::create($validated);

        if ($request->has('doctor_ids')) {
            $result->doctors()->attach($request->input('doctor_ids'));
        }

        return response()->json($result, 201);
    }

    public function show($id)
    {
        $result = Result::with(['clinic', 'doctors', 'type'])->findOrFail($id);
        return response()->json($result);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'patient_identification_number' => 'sometimes|required|string|max:255',
            'clinic_id' => 'sometimes|required|exists:users,id',
            'type_id' => 'sometimes|required|exists:types,id',
            'date' => 'sometimes|required|date',
            'notes' => 'nullable|string',
            'file' => 'nullable|string',
            'doctor_ids' => 'nullable|array',
            'doctor_ids.*' => 'exists:users,id'
        ]);

        $result = Result::findOrFail($id);
        $result->update($validated);

        if ($request->has('doctor_ids')) {
            $result->doctors()->sync($request->input('doctor_ids'));
        }

        return response()->json($result);
    }

    public function destroy($id)
    {
        Result::destroy($id);
        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\TestResult;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class ResultController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        $isDoctor = $user->hasRole('doctor');
        $clinicId = $user->hasRole('clinic') ? $user->id : $user->clinic_id;

        $validated = $request->validate([
            'patientName' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'dob' => 'required|date',
            'idNumber' => 'required|string|max:255',
            'testType' => 'required|string|max:255',
            'doctor' => $isDoctor ? 'nullable|array' : 'required|array',
            'testResult' => 'required|file|mimes:pdf,jpg,png',
        ]);

        $path = $request->file('testResult')->store('test_results', 'public');

        $doctorIds = $isDoctor ? [Auth::id()] : $validated['doctor'];

        $testResult = Result::create([
            'patientName' => $validated['patientName'],
            'surname' => $validated['surname'],
            'dob' => $validated['dob'],
            'idNumber' => $validated['idNumber'],
            'testType' => $validated['testType'],
            'doctor_ids' => json_encode($doctorIds),
            'testResult' => $path,
            'clinic_id' => $clinicId,
        ]);

        return response()->json(['message' => 'Test result added successfully', 'data' => $testResult], 201);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $results = null;

        if ($user->hasRole('clinic')) {
            $query = Result::where('clinic_id', $user->id)->orderBy('created_at', 'desc');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('patientName', 'like', '%' . $search . '%')
                        ->orWhere('surname', 'like', '%' . $search . '%')
                        ->orWhere('idNumber', 'like', '%' . $search . '%');
                });
            }

            $results = $query->paginate($perPage);
        } elseif ($user->hasRole('doctor')) {
            $query = Result::whereJsonContains('doctor_ids', $user->id)->orderBy('created_at', 'desc');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('patientName', 'like', '%' . $search . '%')
                        ->orWhere('surname', 'like', '%' . $search . '%')
                        ->orWhere('idNumber', 'like', '%' . $search . '%');
                });
            }

            $results = $query->paginate($perPage);

            if ($results->isEmpty()) {
                $results = Result::all()->filter(function ($result) use ($user) {
                    $doctorIds = json_decode($result->doctor_ids, true);
                    return in_array($user->id, $doctorIds);
                })->values();

                if ($search) {
                    $results = $results->filter(function ($result) use ($search) {
                        return stripos($result->patientName, $search) !== false ||
                            stripos($result->surname, $search) !== false ||
                            stripos($result->idNumber, $search) !== false;
                    })->values();
                }

                $results = $results->forPage($request->input('page', 1), $perPage);
                $results = new \Illuminate\Pagination\LengthAwarePaginator(
                    $results,
                    $results->count(),
                    $perPage,
                    $request->input('page', 1),
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            }
        } else {
            return response()->json(['message' => 'Unauthorized user role'], 403);
        }

        $results->transform(function ($result) {
            return [
                'id' => $result->id,
                'patientName' => $result->patientName,
                'surname' => $result->surname,
                'dob' => $result->dob,
                'idNumber' => $result->idNumber,
                'testType' => Type::find($result->testType)->name,
                'doctorNames' => User::whereIn('id', json_decode($result->doctor_ids))->pluck('name')->toArray(),
                'testResult' => Storage::url($result->testResult),
                'created_at' => $result->created_at,
                'updated_at' => $result->updated_at,
                'clinic_id' => $result->clinic_id,
            ];
        });

        return response()->json([
            'data' => $results,
            'current_page' => $request->input('page', 1),
            'last_page' => $results->lastPage(),
            'total' => $results->total()
        ]);
    }

    public function destroy($id)
    {
        $result = Result::find($id);
        $result->delete();

        return response()->json(['message' => 'Result deleted']);
    }

    public function show($id)
    {
        $result = Result::find($id);

        return [
            'id' => $result->id,
            'patientName' => $result->patientName,
            'surname' => $result->surname,
            'dob' => $result->dob,
            'idNumber' => $result->idNumber,
            'testType' => $result->testTypeName,
            'doctorNames' => $result->doctorNames,
            'testResult' => Storage::url($result->testResult),
            'created_at' => $result->created_at,
            'updated_at' => $result->updated_at,
            'clinic_id' => $result->clinic_id,
        ];
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'patientName' => 'sometimes|string|max:255',
            'surname' => 'sometimes|string|max:255',
            'dob' => 'sometimes|date',
            'idNumber' => 'sometimes|string|max:255',
            'testType' => 'sometimes|string|max:255',
            'doctor' => 'sometimes|array',
            'testResult' => 'sometimes|file|mimes:pdf,jpg,png',
        ]);

        $result = Result::findOrFail($id);

        $user = Auth::user();
        if ($user->hasRole('clinic')) {
            if ($result->clinic_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } elseif ($user->hasRole('doctor')) {
            if (!in_array($user->id, json_decode($result->doctor_ids, true))) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } else {
            return response()->json(['message' => 'Unauthorized user role'], 403);
        }

        if ($request->hasFile('testResult')) {
            Storage::disk('public')->delete($result->testResult);
            $path = $request->file('testResult')->store('test_results', 'public');
            $result->testResult = $path;
        }

        if ($request->has('doctor')) {
            $doctorIds = User::whereIn('name', $validated['doctor'])
                ->pluck('id')
                ->toArray();
            $result->doctor_ids = json_encode($doctorIds);
        }

        if ($request->has('testType')) {
            $testType = Type::where('name', $validated['testType'])->first();
            if ($testType) {
                $result->testType = $testType->id;
            } else {
                return response()->json(['message' => 'Invalid test type'], 400);
            }
        }

        $result->update(array_filter($validated, function ($key) {
            return $key !== 'doctor' && $key !== 'testType' && $key !== 'testResult';
        }, ARRAY_FILTER_USE_KEY));

        return response()->json(['message' => 'Test result updated successfully', 'data' => $result], 200);
    }
}

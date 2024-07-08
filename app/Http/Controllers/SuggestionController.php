<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SuggestionController extends Controller
{
    public function getSuggestions(Request $request)
    {
        Log::info('Getting suggestions');
        $clinic = $request->user(); // Get the authenticated user
        Log::info('Authenticated user:', ['clinic' => $clinic]);

        $search = $request->input('search');
        Log::info('Search term:', ['search' => $search]);

        // Assuming role 'doctor' is a string in your database
        $doctors = User::role('doctor')
            ->where('clinic_id', $clinic->id) // Assuming you have a 'clinic_id' to link doctors to a clinic
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('surname', 'like', '%' . $search . '%');
                });
            })
            ->get();

        Log::info('Doctors found:', ['doctors' => $doctors]);

        return response()->json($doctors);
    }
}

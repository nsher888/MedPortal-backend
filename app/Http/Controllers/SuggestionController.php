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
        $clinic = $request->user(); // Authenticated user who is a clinic
        Log::info($clinic);

        $search = $request->input('search');

        $doctors = $clinic->doctors()
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('surname', 'like', '%' . $search . '%');
                });
            })
            ->get();

        Log::info($doctors);
        return response()->json($doctors);
    }
}

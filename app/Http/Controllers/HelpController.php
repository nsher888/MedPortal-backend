<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HelpController extends Controller
{
    public function doctorsList()
    {
        $clinic = auth()->user();

        Log::info('Authenticated User:', ['user' => $clinic]);

        $doctors = User::role('doctor')->where('clinic_id', $clinic->id)->get();

        Log::info('Fetched doctors:', ['doctors' => $doctors]);

        return response()->json($doctors);
    }
}

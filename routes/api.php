<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\TypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    $user = $request->user();
    $user->load('roles');

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'idNumber' => $user->idNumber,
        'dateOfBirth' => $user->dateOfBirth,
        'surname' => $user->surname,
        'email_verified_at' => $user->email_verified_at,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
        'roles' => $user->roles->pluck('name')
    ]);
});


Route::get('/doctors', [DoctorController::class, 'index']);
Route::delete('/doctors/{id}', [DoctorController::class, 'destroy']);
Route::get('/doctors/{id}', [DoctorController::class, 'show']);
Route::post('/doctors/{id}', [DoctorController::class, 'updateDoctor']);
Route::post('/doctors', [DoctorController::class, 'store']);


Route::get('/types', [TypeController::class, 'index']);



Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/results', [ResultController::class, 'index']);
    Route::post('/results', [ResultController::class, 'store']);
    Route::get('/results/{id}', [ResultController::class, 'show']);
    Route::put('/results/{id}', [ResultController::class, 'update']);
    Route::delete('/results/{id}', [ResultController::class, 'destroy']);
});

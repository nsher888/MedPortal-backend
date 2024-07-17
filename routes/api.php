<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorAvailabilityController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\StatisticController;
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
Route::get('/doctors/all', [DoctorController::class, 'getAllDoctors']);
Route::delete('/doctors/{id}', [DoctorController::class, 'destroy']);
Route::get('/doctors/{id}', [DoctorController::class, 'show']);
Route::post('/doctors/{id}', [DoctorController::class, 'updateDoctor']);
Route::post('/doctors', [DoctorController::class, 'store']);






Route::get('/types', [TypeController::class, 'index']);


Route::post('/test-results', [ResultController::class, 'store']);
Route::get('/test-results', [ResultController::class, 'index']);
Route::get('/test-results/suggestions', [ResultController::class, 'getResultsSuggestions']);

Route::delete('/test-results/{id}', [ResultController::class, 'destroy']);
Route::get('/test-results/{id}', [ResultController::class, 'show']);


Route::post('/test-results/{id}', [ResultController::class, 'update']);


Route::get('/clinics/statistics', [StatisticController::class, 'getClinicStatistics']);

Route::get('/patients/results', [PatientController::class, 'index']);

Route::get('/patient/clinics', [PatientController::class, 'getAllClinics']);



Route::get('/doctor-list', [HelpController::class, 'doctorsList']);


Route::post('/availabilities', [DoctorAvailabilityController::class, 'store']);
Route::get('/availabilities', [DoctorAvailabilityController::class, 'index']);
Route::post('/availabilities/multiple', [DoctorAvailabilityController::class, 'storeMultiple']);
Route::delete('/availabilities/{id}', [DoctorAvailabilityController::class, 'cancelAvailability']);
Route::get('/time-slots', [DoctorAvailabilityController::class, 'getAvailableTimeSlots']);

Route::get('/time-slots/doctor', [DoctorAvailabilityController::class, 'getDoctorTimeSlots']);
Route::delete('/time-slots/{id}', [DoctorAvailabilityController::class, 'cancelTimeSlot']);
Route::post('/time-slots/{id}', [DoctorAvailabilityController::class, 'changeTimeSlotStatus']);
// Route::post('/time-slots/available/{id}', [DoctorAvailabilityController::class, 'makeTimeSlotAvailable']);


Route::post('/appointments', [AppointmentController::class, 'book']);
Route::get('/appointments', [AppointmentController::class, 'index']);

Route::get('/appointments/dates/{id}', [DoctorAvailabilityController::class, 'getAvailableDates']);
Route::delete('/appointments/{id}', [AppointmentController::class, 'cancelAppointment']);


Route::get('/clinics/{clinicId}/doctors', [DoctorController::class, 'getDoctorsByClinic']);

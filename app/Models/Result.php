<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'patientName',
        'surname',
        'dob',
        'idNumber',
        'testType',
        'doctor_ids',
        'testResult',
        'clinic_id',
    ];

    protected $casts = [
        'doctor_ids' => 'array',
        'dob' => 'date',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class, 'testType', 'id');
    }

    public function getDoctorNamesAttribute()
    {
        if (is_string($this->doctor_ids)) {
            $this->doctor_ids = json_decode($this->doctor_ids, true);
        }
        return User::whereIn('id', $this->doctor_ids)
            ->get()
            ->map(function ($doctor) {
                return $doctor->name . ' ' . $doctor->surname;
            })
            ->toArray();
    }

    public function getTestTypeNameAttribute()
    {
        return $this->type ? $this->type->name : null;
    }
}

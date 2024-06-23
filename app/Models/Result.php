<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'patient_identification_number',
        'clinic_id',
        'type_id',
        'date',
        'notes',
        'file'
    ];

    public function doctors()
    {
        return $this->belongsToMany(User::class, 'doctor_result', 'result_id', 'doctor_id');
    }

    public function clinic()
    {
        return $this->belongsTo(User::class, 'clinic_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}

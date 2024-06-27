<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public function type()
    {
        return $this->belongsTo(Type::class, 'testType', 'id');
    }

    public function getDoctorNamesAttribute()
    {
        return User::whereIn('id', $this->doctor_ids)->pluck('name')->toArray();
    }

    public function getTestTypeNameAttribute()
    {
        return $this->type ? $this->type->name : null;
    }
}

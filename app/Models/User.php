<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'idNumber',
        'dateOfBirth',
        'surname',
        'clinic_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function clinic()
    {
        return $this->belongsTo(User::class, 'clinic_id');
    }

    public function doctors()
    {
        return $this->hasMany(User::class, 'clinic_id');
    }

    public function clinicResults()
    {
        return $this->hasMany(Result::class, 'clinic_id');
    }

    public function doctorResults()
    {
        return $this->belongsToMany(Result::class, 'doctor_result', 'doctor_id', 'result_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

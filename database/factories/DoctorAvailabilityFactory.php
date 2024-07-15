<?php

namespace Database\Factories;

use App\Models\DoctorAvailability;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DoctorAvailabilityFactory extends Factory
{
    protected $model = DoctorAvailability::class;

    public function definition()
    {
        $startTime = '09:00';
        $endTime = '19:00';
        $date = $this->faker->dateTimeBetween('now', '+1 month');

        while (in_array($date->format('l'), ['Sunday'])) {
            $date = $this->faker->dateTimeBetween('now', '+1 month');
        }

        return [
            'doctor_id' => User::factory(),
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }
}

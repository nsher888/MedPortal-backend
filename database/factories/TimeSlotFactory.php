<?php

namespace Database\Factories;

use App\Models\TimeSlot;
use App\Models\DoctorAvailability;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TimeSlotFactory extends Factory
{
    protected $model = TimeSlot::class;

    public function definition()
    {
        return [
            'doctor_id' => DoctorAvailability::factory()->create()->doctor_id,
            'date' => DoctorAvailability::factory()->create()->date,
            'start_time' => Carbon::parse($this->faker->time('H:i'))->format('H:i:s'),
            'status' => 'free',
        ];
    }
}

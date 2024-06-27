<?php

namespace Database\Factories;

use App\Models\Result;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResultFactory extends Factory
{
    protected $model = Result::class;

    public function definition()
    {
        $doctorIds = User::role('doctor')->pluck('id')->random(2)->toArray();
        return [
            'patientName' => $this->faker->firstName,
            'surname' => $this->faker->lastName,
            'dob' => $this->faker->date,
            'idNumber' => Str::random(10),
            'testType' => Type::inRandomOrder()->first()->id,
            'doctor_ids' => json_encode($doctorIds),
            'testResult' => 'test_results/' . $this->faker->uuid . '.pdf',
            'clinic_id' => User::role('clinic')->inRandomOrder()->first()->id,
        ];
    }
}

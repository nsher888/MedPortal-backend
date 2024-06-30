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

    private static $existingPatients = [];

    public function definition()
    {
        $doctorIds = User::role('doctor')->pluck('id')->random(2)->toArray();

        $weightedDates = [
            $this->faker->dateTimeBetween('-1 month', '-20 days'),
            $this->faker->dateTimeBetween('-20 days', '-10 days'),
            $this->faker->dateTimeBetween('-10 days', '-5 days'),
            $this->faker->dateTimeBetween('-5 days', 'now')
        ];
        $createdAt = $this->faker->randomElement(array_merge(
            array_fill(0, 50, $weightedDates[0]),
            array_fill(0, 30, $weightedDates[1]),
            array_fill(0, 15, $weightedDates[2]),
            array_fill(0, 5, $weightedDates[3])
        ));

        $types = Type::pluck('id')->toArray();
        $weightedTypes = [];
        foreach ($types as $type) {
            $weight = $this->faker->numberBetween(1, 10);
            $weightedTypes = array_merge($weightedTypes, array_fill(0, $weight, $type));
        }
        $testType = $this->faker->randomElement($weightedTypes);

        $idNumbersPool = [
            '12345678901', '23456789012', '34567890123', '45678901234',
            '56789012345', '67890123456', '78901234567', '89012345678',
            '90123456789', '01234567890', '11234567890', '12234567890',
            '13234567890', '14234567890', '15234567890', '16234567890',
            '17234567890', '18234567890', '19234567890', '20234567890',
        ];

        $usePoolIdNumber = $this->faker->boolean(60);
        if ($usePoolIdNumber) {
            $idNumber = $this->faker->randomElement($idNumbersPool);
        } else {
            $idNumber = $this->faker->unique()->numerify('###########');
        }

        if (array_key_exists($idNumber, self::$existingPatients)) {
            $patient = self::$existingPatients[$idNumber];
        } else {
            $patient = [
                'patientName' => $this->faker->firstName,
                'surname' => $this->faker->lastName,
                'dob' => $this->faker->date,
            ];
            if ($usePoolIdNumber) {
                self::$existingPatients[$idNumber] = $patient;
            }
        }

        return array_merge($patient, [
            'idNumber' => $idNumber,
            'testType' => $testType,
            'doctor_ids' => json_encode($doctorIds),
            'testResult' => 'test_results/' . $this->faker->uuid . '.pdf',
            'clinic_id' => User::role('clinic')->inRandomOrder()->first()->id,
            'created_at' => $createdAt,
        ]);
    }
}

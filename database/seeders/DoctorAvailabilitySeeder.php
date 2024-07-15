<?php

namespace Database\Seeders;

use App\Models\DoctorAvailability;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DoctorAvailabilitySeeder extends Seeder
{
    public function run()
    {
        $doctors = User::role('doctor')->get();

        foreach ($doctors as $doctor) {
            $availabilities = DoctorAvailability::factory()
                ->count(10)
                ->create(['doctor_id' => $doctor->id]);

            foreach ($availabilities as $availability) {
                $startTime = Carbon::parse($availability->start_time)->setMinute(0);
                $endTime = Carbon::parse($availability->end_time)->setMinute(0);

                if ($startTime->minute % 30 !== 0) {
                    $startTime->minute = ($startTime->minute < 30) ? 30 : 0;
                    if ($startTime->minute == 0) {
                        $startTime->addHour();
                    }
                }

                while ($startTime < $endTime) {
                    TimeSlot::create([
                        'doctor_id' => $availability->doctor_id,
                        'date' => $availability->date,
                        'start_time' => $startTime->format('H:i:s'),
                        'status' => 'free',
                    ]);

                    $startTime->addMinutes(30);
                }
            }
        }
    }
}

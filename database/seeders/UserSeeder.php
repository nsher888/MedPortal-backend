<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $clinicRole = Role::firstOrCreate(['name' => 'clinic']);
        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        $patientRole = Role::firstOrCreate(['name' => 'patient']);

        // Create default clinic users
        $clinics = [
            [
                'name' => 'Clinic One',
                'email' => 'clinic1@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Clinic Two',
                'email' => 'clinic2@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Clinic Three',
                'email' => 'clinic3@example.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($clinics as $clinicData) {
            $clinic = User::create($clinicData);
            $clinic->assignRole($clinicRole);
        }

        // Create doctor users using the factory
        User::factory()->count(100)->create();

        // Create default patient users
        $patients = [
            [
                'name' => 'Patient One',
                'surname' => 'Surname One',
                'email' => 'patient1@example.com',
                'idNumber' => '001',
                'dateOfBirth' => '1990-01-01',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Patient Two',
                'surname' => 'Surname Two',
                'email' => 'patient2@example.com',
                'idNumber' => '002',
                'dateOfBirth' => '1991-02-02',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($patients as $patientData) {
            $patient = User::create($patientData);
            $patient->assignRole($patientRole);
        }
    }
}

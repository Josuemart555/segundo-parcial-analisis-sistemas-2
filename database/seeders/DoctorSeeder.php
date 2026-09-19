<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctores = [
            ['nombre' => 'Dra. Ana Morales', 'especialidad' => 'Medicina General', 'email' => 'ana.morales@his.test', 'telefono' => '5555-1001'],
            ['nombre' => 'Dr. Carlos Pérez', 'especialidad' => 'Pediatría', 'email' => 'carlos.perez@his.test', 'telefono' => '5555-1002'],
            ['nombre' => 'Dra. Lucía Ramírez', 'especialidad' => 'Cardiología', 'email' => 'lucia.ramirez@his.test', 'telefono' => '5555-1003'],
            ['nombre' => 'Dr. Miguel Sánchez', 'especialidad' => 'Traumatología', 'email' => 'miguel.sanchez@his.test', 'telefono' => '5555-1004'],
            ['nombre' => 'Dra. Paola Gómez', 'especialidad' => 'Dermatología', 'email' => 'paola.gomez@his.test', 'telefono' => '5555-1005'],
        ];

        foreach ($doctores as $doctor) {
            Doctor::updateOrCreate(['email' => $doctor['email']], $doctor);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@his.test'],
            ['name' => 'Administrador HIS', 'password' => 'password', 'activo' => true]
        );
        $admin->syncRoles(['admin']);

        $recepcionista = User::updateOrCreate(
            ['email' => 'recepcion@his.test'],
            ['name' => 'Recepción HIS', 'password' => 'password', 'activo' => true]
        );
        $recepcionista->syncRoles(['recepcionista']);

        $doctores = [
            ['nombre' => 'Dra. Ana Morales', 'especialidad' => 'Medicina General', 'email' => 'ana.morales@his.test', 'telefono' => '5555-1001'],
            ['nombre' => 'Dr. Carlos Pérez', 'especialidad' => 'Pediatría', 'email' => 'carlos.perez@his.test', 'telefono' => '5555-1002'],
            ['nombre' => 'Dra. Lucía Ramírez', 'especialidad' => 'Cardiología', 'email' => 'lucia.ramirez@his.test', 'telefono' => '5555-1003'],
            ['nombre' => 'Dr. Miguel Sánchez', 'especialidad' => 'Traumatología', 'email' => 'miguel.sanchez@his.test', 'telefono' => '5555-1004'],
            ['nombre' => 'Dra. Paola Gómez', 'especialidad' => 'Dermatología', 'email' => 'paola.gomez@his.test', 'telefono' => '5555-1005'],
        ];

        foreach ($doctores as $doctor) {
            $especialidad = Especialidad::where('nombre', $doctor['especialidad'])->first();

            $user = User::updateOrCreate(
                ['email' => $doctor['email']],
                [
                    'name' => $doctor['nombre'],
                    'password' => 'password',
                    'telefono' => $doctor['telefono'],
                    'especialidad_id' => $especialidad?->id,
                    'activo' => true,
                ]
            );
            $user->syncRoles(['doctor']);
        }
    }
}

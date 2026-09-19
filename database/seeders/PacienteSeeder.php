<?php

namespace Database\Seeders;

use App\Models\Paciente;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = [
            ['nombre' => 'José Martínez', 'documento' => '1001-0001', 'telefono' => '5555-2001', 'email' => 'jose.martinez@correo.test', 'fecha_nacimiento' => '1990-03-12'],
            ['nombre' => 'María López', 'documento' => '1001-0002', 'telefono' => '5555-2002', 'email' => 'maria.lopez@correo.test', 'fecha_nacimiento' => '1985-07-22'],
            ['nombre' => 'Andrés Castillo', 'documento' => '1001-0003', 'telefono' => '5555-2003', 'email' => 'andres.castillo@correo.test', 'fecha_nacimiento' => '2001-11-05'],
            ['nombre' => 'Fernanda Ruiz', 'documento' => '1001-0004', 'telefono' => '5555-2004', 'email' => 'fernanda.ruiz@correo.test', 'fecha_nacimiento' => '1978-01-30'],
            ['nombre' => 'Diego Herrera', 'documento' => '1001-0005', 'telefono' => '5555-2005', 'email' => 'diego.herrera@correo.test', 'fecha_nacimiento' => '1995-09-17'],
            ['nombre' => 'Valeria Torres', 'documento' => '1001-0006', 'telefono' => '5555-2006', 'email' => 'valeria.torres@correo.test', 'fecha_nacimiento' => '2010-05-02'],
            ['nombre' => 'Ricardo Vega', 'documento' => '1001-0007', 'telefono' => '5555-2007', 'email' => 'ricardo.vega@correo.test', 'fecha_nacimiento' => '1966-12-19'],
        ];

        foreach ($pacientes as $paciente) {
            Paciente::updateOrCreate(['documento' => $paciente['documento']], $paciente);
        }
    }
}

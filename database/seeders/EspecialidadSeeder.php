<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Seeder;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            'Medicina General',
            'Pediatría',
            'Cardiología',
            'Traumatología',
            'Dermatología',
        ];

        foreach ($especialidades as $nombre) {
            Especialidad::updateOrCreate(['nombre' => $nombre], ['activa' => true]);
        }
    }
}

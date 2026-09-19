<?php

namespace Database\Seeders;

use App\Models\EstadoCita;
use Illuminate\Database\Seeder;

class EstadoCitaSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['slug' => 'pendiente', 'nombre' => 'Pendiente', 'color' => '#f59e0b', 'es_terminal' => false, 'bloquea_horario' => true, 'orden' => 1],
            ['slug' => 'confirmada', 'nombre' => 'Confirmada', 'color' => '#2563eb', 'es_terminal' => false, 'bloquea_horario' => true, 'orden' => 2],
            ['slug' => 'atendida', 'nombre' => 'Atendida', 'color' => '#16a34a', 'es_terminal' => true, 'bloquea_horario' => true, 'orden' => 3],
            ['slug' => 'cancelada', 'nombre' => 'Cancelada', 'color' => '#6b7280', 'es_terminal' => true, 'bloquea_horario' => false, 'orden' => 4],
        ];

        foreach ($estados as $estado) {
            EstadoCita::updateOrCreate(['slug' => $estado['slug']], $estado);
        }
    }
}

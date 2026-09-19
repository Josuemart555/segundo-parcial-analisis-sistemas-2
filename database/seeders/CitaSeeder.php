<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CitaSeeder extends Seeder
{
    public function run(): void
    {
        $doctores = Doctor::orderBy('id')->get();
        $pacientes = Paciente::orderBy('id')->get();

        if ($doctores->isEmpty() || $pacientes->isEmpty()) {
            return;
        }

        $hoy = Carbon::now()->startOfDay();

        $citas = [
            ['doctor' => 0, 'paciente' => 0, 'inicio' => $hoy->copy()->addDay()->setTime(9, 0), 'motivo' => 'Consulta general', 'estado' => Cita::ESTADO_CONFIRMADA],
            ['doctor' => 1, 'paciente' => 1, 'inicio' => $hoy->copy()->addDay()->setTime(10, 30), 'motivo' => 'Control pediátrico', 'estado' => Cita::ESTADO_PENDIENTE],
            ['doctor' => 2, 'paciente' => 2, 'inicio' => $hoy->copy()->addDays(2)->setTime(8, 0), 'motivo' => 'Evaluación cardiológica', 'estado' => Cita::ESTADO_PENDIENTE],
            ['doctor' => 3, 'paciente' => 3, 'inicio' => $hoy->copy()->addDays(2)->setTime(14, 0), 'motivo' => 'Dolor de rodilla', 'estado' => Cita::ESTADO_CONFIRMADA],
            ['doctor' => 0, 'paciente' => 4, 'inicio' => $hoy->copy()->subDays(3)->setTime(11, 0), 'motivo' => 'Chequeo anual', 'estado' => Cita::ESTADO_ATENDIDA],
            ['doctor' => 4, 'paciente' => 5, 'inicio' => $hoy->copy()->addDays(1)->setTime(16, 0), 'motivo' => 'Consulta dermatológica', 'estado' => Cita::ESTADO_CANCELADA],
        ];

        foreach ($citas as $c) {
            Cita::updateOrCreate(
                [
                    'doctor_id' => $doctores[$c['doctor']]->id,
                    'paciente_id' => $pacientes[$c['paciente']]->id,
                    'fecha_inicio' => $c['inicio'],
                ],
                [
                    'fecha_fin' => $c['inicio']->copy()->addMinutes(30),
                    'motivo' => $c['motivo'],
                    'estado' => $c['estado'],
                ]
            );
        }
    }
}

<?php

namespace App\Services;

use App\Models\Cita;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CitaService
{
    public function listar(array $filtros): Collection
    {
        $query = Cita::query()->with(['doctor', 'paciente']);

        if (! empty($filtros['doctor_id'])) {
            $query->where('doctor_id', $filtros['doctor_id']);
        }

        if (! empty($filtros['paciente_id'])) {
            $query->where('paciente_id', $filtros['paciente_id']);
        }

        if (! empty($filtros['desde'])) {
            $query->where('fecha_fin', '>=', $filtros['desde']);
        }

        if (! empty($filtros['hasta'])) {
            $query->where('fecha_inicio', '<=', $filtros['hasta']);
        }

        return $query->orderBy('fecha_inicio')->get();
    }

    public function crear(array $datos): Cita
    {
        return DB::transaction(function () use ($datos) {
            return Cita::create([
                'paciente_id' => $datos['paciente_id'],
                'doctor_id' => $datos['doctor_id'],
                'fecha_inicio' => $datos['fecha_inicio'],
                'fecha_fin' => $datos['fecha_fin'],
                'motivo' => $datos['motivo'],
                'estado' => Cita::ESTADO_PENDIENTE,
            ]);
        });
    }

    public function reprogramar(Cita $cita, array $datos): Cita
    {
        return DB::transaction(function () use ($cita, $datos) {
            $cita->fill([
                'doctor_id' => $datos['doctor_id'] ?? $cita->doctor_id,
                'paciente_id' => $datos['paciente_id'] ?? $cita->paciente_id,
                'fecha_inicio' => $datos['fecha_inicio'],
                'fecha_fin' => $datos['fecha_fin'],
                'motivo' => $datos['motivo'] ?? $cita->motivo,
            ]);
            $cita->save();

            return $cita->fresh(['doctor', 'paciente']);
        });
    }
}

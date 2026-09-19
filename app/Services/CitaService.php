<?php

namespace App\Services;

use App\Exceptions\CitaConflictoException;
use App\Exceptions\TransicionEstadoInvalidaException;
use App\Models\Cita;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CitaService
{
    /**
     * Transiciones de estado permitidas. Cancelada y atendida son estados
     * terminales: una vez alcanzados, la cita no puede volver a moverse.
     */
    private const TRANSICIONES_PERMITIDAS = [
        Cita::ESTADO_PENDIENTE => [Cita::ESTADO_CONFIRMADA, Cita::ESTADO_CANCELADA, Cita::ESTADO_ATENDIDA],
        Cita::ESTADO_CONFIRMADA => [Cita::ESTADO_CANCELADA, Cita::ESTADO_ATENDIDA],
        Cita::ESTADO_CANCELADA => [],
        Cita::ESTADO_ATENDIDA => [],
    ];

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
            $this->verificarDisponibilidad(
                doctorId: $datos['doctor_id'],
                fechaInicio: $datos['fecha_inicio'],
                fechaFin: $datos['fecha_fin'],
            );

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
            $doctorId = $datos['doctor_id'] ?? $cita->doctor_id;

            $this->verificarDisponibilidad(
                doctorId: $doctorId,
                fechaInicio: $datos['fecha_inicio'],
                fechaFin: $datos['fecha_fin'],
                excluirCitaId: $cita->id,
            );

            $cita->fill([
                'doctor_id' => $doctorId,
                'paciente_id' => $datos['paciente_id'] ?? $cita->paciente_id,
                'fecha_inicio' => $datos['fecha_inicio'],
                'fecha_fin' => $datos['fecha_fin'],
                'motivo' => $datos['motivo'] ?? $cita->motivo,
            ]);
            $cita->save();

            return $cita->fresh(['doctor', 'paciente']);
        });
    }

    public function cambiarEstado(Cita $cita, string $nuevoEstado): Cita
    {
        $permitidas = self::TRANSICIONES_PERMITIDAS[$cita->estado] ?? [];

        if ($cita->estado !== $nuevoEstado && ! in_array($nuevoEstado, $permitidas, true)) {
            throw new TransicionEstadoInvalidaException(
                "No se puede cambiar la cita de estado '{$cita->estado}' a '{$nuevoEstado}'."
            );
        }

        $cita->estado = $nuevoEstado;
        $cita->save();

        return $cita->fresh(['doctor', 'paciente']);
    }

    /**
     * Valida en el servidor que el doctor no tenga otra cita activa que se
     * solape con el rango de horario solicitado (RQF-03, RQNF-07).
     */
    private function verificarDisponibilidad(
        int $doctorId,
        string $fechaInicio,
        string $fechaFin,
        ?int $excluirCitaId = null,
    ): void {
        $query = Cita::query()
            ->where('doctor_id', $doctorId)
            ->where('estado', '!=', Cita::ESTADO_CANCELADA)
            ->where('fecha_inicio', '<', $fechaFin)
            ->where('fecha_fin', '>', $fechaInicio);

        if ($excluirCitaId !== null) {
            $query->where('id', '!=', $excluirCitaId);
        }

        if ($query->exists()) {
            throw new CitaConflictoException();
        }
    }
}

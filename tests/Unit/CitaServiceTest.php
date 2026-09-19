<?php

namespace Tests\Unit;

use App\Exceptions\CitaConflictoException;
use App\Exceptions\TransicionEstadoInvalidaException;
use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\Paciente;
use App\Models\User;
use App\Services\CitaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CitaServiceTest extends TestCase
{
    use RefreshDatabase;

    private CitaService $service;
    private User $doctor;
    private Paciente $paciente;

    protected function setUp(): void
    {
        parent::setUp();

        EstadoCita::create(['slug' => 'pendiente', 'nombre' => 'Pendiente', 'color' => '#f59e0b', 'es_terminal' => false, 'bloquea_horario' => true, 'orden' => 1]);
        EstadoCita::create(['slug' => 'confirmada', 'nombre' => 'Confirmada', 'color' => '#2563eb', 'es_terminal' => false, 'bloquea_horario' => true, 'orden' => 2]);
        EstadoCita::create(['slug' => 'cancelada', 'nombre' => 'Cancelada', 'color' => '#6b7280', 'es_terminal' => true, 'bloquea_horario' => false, 'orden' => 3]);
        EstadoCita::create(['slug' => 'atendida', 'nombre' => 'Atendida', 'color' => '#16a34a', 'es_terminal' => true, 'bloquea_horario' => true, 'orden' => 4]);

        Role::create(['name' => 'doctor']);
        $this->doctor = User::factory()->create();
        $this->doctor->assignRole('doctor');

        $this->paciente = Paciente::create([
            'nombre' => 'Paciente Test',
            'documento' => '0001',
        ]);

        $this->service = new CitaService();
    }

    public function test_no_permite_crear_una_cita_solapada_para_el_mismo_doctor(): void
    {
        $this->service->crear([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctor->id,
            'fecha_inicio' => '2026-01-01 09:00:00',
            'fecha_fin' => '2026-01-01 09:30:00',
            'motivo' => 'Consulta',
        ]);

        $this->expectException(CitaConflictoException::class);

        $this->service->crear([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctor->id,
            'fecha_inicio' => '2026-01-01 09:15:00',
            'fecha_fin' => '2026-01-01 09:45:00',
            'motivo' => 'Conflicto',
        ]);
    }

    public function test_bloquea_cambiar_de_estado_una_cita_en_estado_terminal(): void
    {
        $cita = $this->service->crear([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctor->id,
            'fecha_inicio' => '2026-01-02 09:00:00',
            'fecha_fin' => '2026-01-02 09:30:00',
            'motivo' => 'Consulta',
        ]);

        $cita = $this->service->cambiarEstado($cita, 'cancelada');

        $this->expectException(TransicionEstadoInvalidaException::class);

        $this->service->cambiarEstado($cita, 'confirmada');
    }
}

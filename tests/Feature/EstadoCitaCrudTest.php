<?php

namespace Tests\Feature;

use App\Models\EstadoCita;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EstadoCitaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_admin_puede_editar_y_actualizar_un_estado_de_cita(): void
    {
        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $estado = EstadoCita::create([
            'slug' => 'pendiente',
            'nombre' => 'Pendiente',
            'color' => '#f59e0b',
            'es_terminal' => false,
            'bloquea_horario' => true,
            'orden' => 1,
        ]);

        $edit = $this->actingAs($admin)->get(route('estados-cita.edit', $estado));
        $edit->assertOk();

        $update = $this->actingAs($admin)->put(route('estados-cita.update', $estado), [
            'slug' => 'pendiente',
            'nombre' => 'Pendiente de confirmación',
            'color' => '#f59e0b',
            'orden' => 1,
        ]);

        $update->assertRedirect(route('estados-cita.index'));
        $this->assertDatabaseHas('estados_cita', ['id' => $estado->id, 'nombre' => 'Pendiente de confirmación']);
    }
}

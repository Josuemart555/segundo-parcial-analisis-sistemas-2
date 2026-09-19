<?php

namespace Tests\Feature;

use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EspecialidadCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_un_admin_puede_crear_una_especialidad(): void
    {
        $response = $this->actingAs($this->admin)->post('/especialidades', [
            'nombre' => 'Neurología',
            'activa' => '1',
        ]);

        $response->assertRedirect('/especialidades');
        $this->assertDatabaseHas('especialidades', ['nombre' => 'Neurología']);
    }

    public function test_no_permite_nombres_de_especialidad_duplicados(): void
    {
        Especialidad::create(['nombre' => 'Cardiología', 'activa' => true]);

        $response = $this->actingAs($this->admin)->post('/especialidades', [
            'nombre' => 'Cardiología',
            'activa' => '1',
        ]);

        $response->assertSessionHasErrors('nombre');
        $this->assertSame(1, Especialidad::where('nombre', 'Cardiología')->count());
    }

    public function test_un_admin_puede_editar_y_actualizar_una_especialidad(): void
    {
        $especialidad = Especialidad::create(['nombre' => 'Cardiología', 'activa' => true]);

        $edit = $this->actingAs($this->admin)->get(route('especialidades.edit', $especialidad));
        $edit->assertOk();

        $update = $this->actingAs($this->admin)->put(route('especialidades.update', $especialidad), [
            'nombre' => 'Cardiología Pediátrica',
            'activa' => '1',
        ]);

        $update->assertRedirect(route('especialidades.index'));
        $this->assertDatabaseHas('especialidades', ['id' => $especialidad->id, 'nombre' => 'Cardiología Pediátrica']);
    }
}

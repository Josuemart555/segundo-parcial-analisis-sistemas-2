<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UsuariosAccesoTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_sin_rol_admin_no_puede_listar_usuarios(): void
    {
        Role::create(['name' => 'doctor']);
        $doctor = User::factory()->create();
        $doctor->assignRole('doctor');

        $response = $this->actingAs($doctor)->get('/usuarios');

        $response->assertForbidden();
    }

    public function test_un_administrador_puede_listar_usuarios(): void
    {
        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/usuarios');

        $response->assertOk();
    }
}

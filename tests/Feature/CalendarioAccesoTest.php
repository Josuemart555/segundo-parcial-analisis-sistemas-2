<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CalendarioAccesoTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_invitado_es_redirigido_al_login(): void
    {
        $response = $this->get('/calendario');

        $response->assertRedirect('/login');
    }

    public function test_un_usuario_autenticado_puede_ver_el_calendario(): void
    {
        Role::create(['name' => 'doctor']);
        $usuario = User::factory()->create();
        $usuario->assignRole('doctor');

        $response = $this->actingAs($usuario)->get('/calendario');

        $response->assertOk();
    }
}

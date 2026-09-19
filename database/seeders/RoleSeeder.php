<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permisos = [
            'gestionar-especialidades',
            'gestionar-estados-cita',
            'gestionar-usuarios',
            'gestionar-citas',
            'ver-calendario',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permisos);

        $recepcionista = Role::firstOrCreate(['name' => 'recepcionista']);
        $recepcionista->syncPermissions(['gestionar-citas', 'ver-calendario']);

        $doctor = Role::firstOrCreate(['name' => 'doctor']);
        $doctor->syncPermissions(['ver-calendario']);
    }
}

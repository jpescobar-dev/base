<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'ver dashboard',
            'gestionar usuarios',
            'gestionar roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $usuario = Role::firstOrCreate(['name' => 'Usuario']);

        $admin->givePermissionTo($permissions);
        $usuario->givePermissionTo(['ver dashboard']);
    }
}
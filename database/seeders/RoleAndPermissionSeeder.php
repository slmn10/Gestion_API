<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les rôles
        $roles = [
            'superAdmin',
            'admin',
            'visiteur',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // Créer les permissions
        $permissions = [
            'view dashboard',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assigner les permissions aux rôles administratifs
        $adminRoles = ['superAdmin', 'admin', 'visiteur'];

        foreach ($adminRoles as $role) {
            $roleInstance = Role::findByName($role);
            $roleInstance->givePermissionTo($permissions);
        }
    }
}


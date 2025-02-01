<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Souleymane HIGUIOH',
                'email' => 'souley@gmail.com',
                'roles' => ['superAdmin', 'admin'], // Plusieurs rôles
                'password' => '12345678',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Samiou BONI ',
                'email' => 'samiou@gmail.com',
                'roles' => ['admin'], // Un seul rôle
                'password' => 'samleroi',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($admins as $adminData) {
            $user = User::create([
                'name' => $adminData['name'],
                'email' => $adminData['email'],
                'email_verified_at' => $adminData['email_verified_at'],
                'password' => Hash::make($adminData['password']),
            ]);

            // Assigner plusieurs rôles si nécessaire
            $user->syncRoles($adminData['roles']);
        }
    }

}


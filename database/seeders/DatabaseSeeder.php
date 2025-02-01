<?php

namespace Database\Seeders;

use App\Models\Boutique;
use App\Models\Produit;
use Database\Seeders\RoleAndPermissionSeeder as SeedersRoleAndPermissionSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SeedersRoleAndPermissionSeeder::class,
            AdminSeeder::class,
            // ProduitSeeder::class,
        ]);
    }
}

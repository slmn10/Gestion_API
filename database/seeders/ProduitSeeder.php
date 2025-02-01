<?php

namespace Database\Seeders;

use App\Models\Produit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProduitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produits = [
            'Pondeuse',
            'Goliat',
            'Coquelet',
            'Poulet de Chair',
            'Pintade',
            'Dindon',
            'Canas',
        ];

        foreach ($produits as $produit) {
            Produit::create(['name' => $produit, 'created_by' => 1]);
        }
    }
}

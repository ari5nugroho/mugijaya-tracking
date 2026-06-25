<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Peralatan elektronik dan gadget canggih.',
                'status' => true,
            ],
            [
                'name' => 'Furniture',
                'slug' => 'furniture',
                'description' => 'Furnitur dan dekorasi kantor/rumah berkualitas.',
                'status' => true,
            ],
            [
                'name' => 'Food & Beverage',
                'slug' => 'food-beverage',
                'description' => 'Makanan dan minuman siap saji serta bahan mentah.',
                'status' => true,
            ],
            [
                'name' => 'Apparel',
                'slug' => 'apparel',
                'description' => 'Pakaian, kaos, celana, dan aksesoris fashion.',
                'status' => true,
            ],
            [
                'name' => 'Household',
                'slug' => 'household',
                'description' => 'Kebutuhan rumah tangga sehari-hari.',
                'status' => true,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}

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
                'name' => 'Kaca',
                'slug' => 'kaca',
                'description' => 'Bahan baku kaca lembaran berbagai jenis dan ketebalan.',
                'status' => true,
            ],
            [
                'name' => 'Aluminium',
                'slug' => 'aluminium',
                'description' => 'Bahan baku profil aluminium dan panel composite.',
                'status' => true,
            ],
            [
                'name' => 'Aksesoris',
                'slug' => 'aksesoris',
                'description' => 'Aksesoris dan bahan pendukung pemasangan kaca/aluminium (engsel, roda sliding, sealant, dll).',
                'status' => true,
            ],
            [
                'name' => 'Fabrikasi Kaca',
                'slug' => 'fabrikasi-kaca',
                'description' => 'Kategori jenis fabrikasi/produk custom berbahan dasar kaca.',
                'status' => true,
            ],
            [
                'name' => 'Fabrikasi Aluminium',
                'slug' => 'fabrikasi-aluminium',
                'description' => 'Kategori jenis fabrikasi/produk custom berbahan dasar aluminium.',
                'status' => true,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}

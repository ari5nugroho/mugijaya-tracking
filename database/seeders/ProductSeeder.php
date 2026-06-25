<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::where('slug', 'electronics')->first();
        $furniture = Category::where('slug', 'furniture')->first();
        $foodBeverage = Category::where('slug', 'food-beverage')->first();
        $apparel = Category::where('slug', 'apparel')->first();
        $household = Category::where('slug', 'household')->first();

        $products = [
            [
                'sku' => 'PRD-ELC-001',
                'name' => 'Smart TV LED 43 Inch',
                'category_id' => $electronics->id,
                'weight' => 8.5,
                'unit' => 'Pcs',
                'price' => 3500000,
                'length' => 96.0,
                'width' => 15.0,
                'height' => 56.0,
                'description' => 'Televisi pintar LED resolusi Full HD dengan fitur internet.',
                'status' => true,
            ],
            [
                'sku' => 'PRD-FNT-023',
                'name' => 'Kursi Kantor Ergonomis',
                'category_id' => $furniture->id,
                'weight' => 12.0,
                'unit' => 'Pcs',
                'price' => 1200000,
                'length' => 60.0,
                'width' => 60.0,
                'height' => 110.0,
                'description' => 'Kursi kerja ergonomis dengan sandaran jala dan penopang pinggang.',
                'status' => true,
            ],
            [
                'sku' => 'PRD-FNB-102',
                'name' => 'Kopi Arabika Java Blend 1Kg',
                'category_id' => $foodBeverage->id,
                'weight' => 1.0,
                'unit' => 'Pack',
                'price' => 150000,
                'length' => 12.0,
                'width' => 8.0,
                'height' => 25.0,
                'description' => 'Biji kopi arabika Jawa pilihan kualitas ekspor kemasan 1 kg.',
                'status' => true,
            ],
            [
                'sku' => 'PRD-CLT-055',
                'name' => 'Kaos Polo Cotton Combed XL',
                'category_id' => $apparel->id,
                'weight' => 0.25,
                'unit' => 'Pcs',
                'price' => 85000,
                'length' => 30.0,
                'width' => 22.0,
                'height' => 2.0,
                'description' => 'Kaos polo bahan katun combed adem ukuran XL.',
                'status' => true,
            ],
            [
                'sku' => 'PRD-HSH-089',
                'name' => 'Pembersih Udara HEPA Filter',
                'category_id' => $household->id,
                'weight' => 4.2,
                'unit' => 'Pcs',
                'price' => 1800000,
                'length' => 35.0,
                'width' => 35.0,
                'height' => 60.0,
                'description' => 'Air purifier dengan HEPA filter untuk membersihkan udara ruangan.',
                'status' => true,
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['sku' => $p['sku']], $p);
        }
    }
}

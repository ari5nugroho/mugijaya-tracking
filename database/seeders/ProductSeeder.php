<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Seeder ini diisi data bahan baku (raw material) kaca & aluminium
     * dengan ukuran standar lembaran/batang dari supplier.
     * Kolom length/width/height dipakai sebagai dimensi standar bahan
     * (bukan barang jadi), unit mengikuti satuan jual umum di industri ini.
     */
    public function run(): void
    {
        $kaca = Category::where('slug', 'kaca')->first();
        $aluminium = Category::where('slug', 'aluminium')->first();
        $aksesoris = Category::where('slug', 'aksesoris')->first();

        $products = [
            // --- Kaca ---
            [
                'sku' => 'KAC-BNG-5MM',
                'name' => 'Kaca Bening 5mm (240x120cm)',
                'category_id' => $kaca->id,
                'weight' => 36.0,
                'unit' => 'Lembar',
                'price' => 380000,
                'length' => 240.0,
                'width' => 120.0,
                'height' => 0.5,
                'description' => 'Kaca bening polos tebal 5mm, ukuran standar lembaran 240x120cm.',
                'status' => true,
            ],
            [
                'sku' => 'KAC-RYB-5MM',
                'name' => 'Kaca Rayben 5mm (240x120cm)',
                'category_id' => $kaca->id,
                'weight' => 36.0,
                'unit' => 'Lembar',
                'price' => 420000,
                'length' => 240.0,
                'width' => 120.0,
                'height' => 0.5,
                'description' => 'Kaca rayben (tinted) tebal 5mm, ukuran standar lembaran 240x120cm.',
                'status' => true,
            ],
            [
                'sku' => 'KAC-TMP-8MM',
                'name' => 'Kaca Tempered 8mm (240x120cm)',
                'category_id' => $kaca->id,
                'weight' => 58.0,
                'unit' => 'Lembar',
                'price' => 950000,
                'length' => 240.0,
                'width' => 120.0,
                'height' => 0.8,
                'description' => 'Kaca tempered tebal 8mm untuk kebutuhan keamanan ekstra, ukuran standar lembaran.',
                'status' => true,
            ],
            [
                'sku' => 'KAC-CRM-5MM',
                'name' => 'Kaca Es Cermin 5mm (240x120cm)',
                'category_id' => $kaca->id,
                'weight' => 36.0,
                'unit' => 'Lembar',
                'price' => 410000,
                'length' => 240.0,
                'width' => 120.0,
                'height' => 0.5,
                'description' => 'Kaca es/cermin motif tebal 5mm, ukuran standar lembaran 240x120cm.',
                'status' => true,
            ],

            // --- Aluminium ---
            [
                'sku' => 'ALM-KSN-4IN',
                'name' => 'Profil Aluminium Kusen 4 Inch (4m)',
                'category_id' => $aluminium->id,
                'weight' => 5.2,
                'unit' => 'Batang',
                'price' => 185000,
                'length' => 400.0,
                'width' => 10.16,
                'height' => 10.16,
                'description' => 'Batang profil aluminium untuk kusen, panjang standar 4 meter, lebar 4 inch.',
                'status' => true,
            ],
            [
                'sku' => 'ALM-KSN-3IN',
                'name' => 'Profil Aluminium Kusen 3 Inch (4m)',
                'category_id' => $aluminium->id,
                'weight' => 4.1,
                'unit' => 'Batang',
                'price' => 150000,
                'length' => 400.0,
                'width' => 7.62,
                'height' => 7.62,
                'description' => 'Batang profil aluminium untuk kusen, panjang standar 4 meter, lebar 3 inch.',
                'status' => true,
            ],
            [
                'sku' => 'ALM-RNG-SLD',
                'name' => 'Aluminium Rangka Sliding (4m)',
                'category_id' => $aluminium->id,
                'weight' => 4.8,
                'unit' => 'Batang',
                'price' => 165000,
                'length' => 400.0,
                'width' => 8.0,
                'height' => 8.0,
                'description' => 'Batang profil aluminium untuk rangka pintu/jendela sliding, panjang standar 4 meter.',
                'status' => true,
            ],
            [
                'sku' => 'ALM-COR-2X4',
                'name' => 'Aluminium Composite Panel (244x122cm)',
                'category_id' => $aluminium->id,
                'weight' => 12.5,
                'unit' => 'Lembar',
                'price' => 720000,
                'length' => 244.0,
                'width' => 122.0,
                'height' => 0.4,
                'description' => 'Panel aluminium composite (ACP) untuk fasad/plafon, ukuran standar lembaran.',
                'status' => true,
            ],

            // --- Aksesoris pendukung ---
            [
                'sku' => 'AKS-KRT-SLD',
                'name' => 'Roda Sliding Kaca/Aluminium',
                'category_id' => $aksesoris->id,
                'weight' => 0.15,
                'unit' => 'Pcs',
                'price' => 25000,
                'length' => 5.0,
                'width' => 3.0,
                'height' => 2.0,
                'description' => 'Roda sliding untuk pintu/jendela kaca atau aluminium.',
                'status' => true,
            ],
            [
                'sku' => 'AKS-KRT-ENG',
                'name' => 'Engsel Pintu Kaca Frameless',
                'category_id' => $aksesoris->id,
                'weight' => 0.4,
                'unit' => 'Pcs',
                'price' => 95000,
                'length' => 10.0,
                'width' => 6.0,
                'height' => 3.0,
                'description' => 'Engsel khusus untuk pintu kaca frameless, bahan stainless.',
                'status' => true,
            ],
            [
                'sku' => 'AKS-SLN-SLC',
                'name' => 'Sealant Silikon Kaca (300ml)',
                'category_id' => $aksesoris->id,
                'weight' => 0.35,
                'unit' => 'Tube',
                'price' => 45000,
                'length' => 25.0,
                'width' => 5.0,
                'height' => 5.0,
                'description' => 'Sealant silikon untuk perekat dan penyegel sambungan kaca.',
                'status' => true,
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['sku' => $p['sku']], $p);
        }
    }
}

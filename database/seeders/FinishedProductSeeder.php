<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\FinishedProduct;

class FinishedProductSeeder extends Seeder
{
    public function run(): void
    {
        $fabKaca = Category::where('slug', 'fabrikasi-kaca')->first();
        $fabAlm = Category::where('slug', 'fabrikasi-aluminium')->first();

        $finishedProducts = [
            // --- Fabrikasi Kaca ---
            [
                'code' => 'FAB-KAC-001',
                'name' => 'Pintu Kaca Frameless',
                'category_id' => $fabKaca?->id,
                'price_per_m2' => 1250000,
                'material_notes' => 'Menggunakan kaca tempered 10-12mm, engsel & handle stainless.',
                'description' => 'Pintu kaca tanpa bingkai (frameless) untuk kantor, ruko, atau rumah modern.',
                'status' => true,
            ],
            [
                'code' => 'FAB-KAC-002',
                'name' => 'Partisi Kaca Kantor',
                'category_id' => $fabKaca?->id,
                'price_per_m2' => 850000,
                'material_notes' => 'Menggunakan kaca tempered 8mm dengan rangka aluminium tipis.',
                'description' => 'Partisi pembatas ruangan kantor berbahan kaca, custom sesuai layout.',
                'status' => true,
            ],
            [
                'code' => 'FAB-KAC-003',
                'name' => 'Cermin Dinding Custom',
                'category_id' => $fabKaca?->id,
                'price_per_m2' => 320000,
                'material_notes' => 'Menggunakan kaca cermin 5mm, finishing tepi dipoles halus.',
                'description' => 'Cermin dinding custom ukuran sesuai kebutuhan ruangan.',
                'status' => true,
            ],
            [
                'code' => 'FAB-KAC-004',
                'name' => 'Shower Screen Kamar Mandi',
                'category_id' => $fabKaca?->id,
                'price_per_m2' => 980000,
                'material_notes' => 'Kaca tempered 8mm dengan lapisan anti air (coating), aksesoris stainless anti karat.',
                'description' => 'Pembatas shower kamar mandi berbahan kaca tempered, tahan air.',
                'status' => true,
            ],

            // --- Fabrikasi Aluminium ---
            [
                'code' => 'FAB-ALM-001',
                'name' => 'Kusen Pintu Aluminium',
                'category_id' => $fabAlm?->id,
                'price_per_m2' => 650000,
                'material_notes' => 'Menggunakan profil aluminium kusen 4 inch, finishing powder coating.',
                'description' => 'Kusen pintu aluminium custom sesuai ukuran bukaan dinding.',
                'status' => true,
            ],
            [
                'code' => 'FAB-ALM-002',
                'name' => 'Jendela Aluminium Sliding',
                'category_id' => $fabAlm?->id,
                'price_per_m2' => 720000,
                'material_notes' => 'Menggunakan rangka aluminium sliding, roda sliding, dan kaca bening 5mm.',
                'description' => 'Jendela geser (sliding) berbahan aluminium dengan kaca bening.',
                'status' => true,
            ],
            [
                'code' => 'FAB-ALM-003',
                'name' => 'Pintu Aluminium Swing',
                'category_id' => $fabAlm?->id,
                'price_per_m2' => 780000,
                'material_notes' => 'Menggunakan profil aluminium kusen 4 inch dan engsel berat.',
                'description' => 'Pintu aluminium model buka-tutup (swing) untuk ruko/rumah.',
                'status' => true,
            ],
            [
                'code' => 'FAB-ALM-004',
                'name' => 'Fasad ACP (Aluminium Composite Panel)',
                'category_id' => $fabAlm?->id,
                'price_per_m2' => 540000,
                'material_notes' => 'Menggunakan panel ACP dan rangka hollow aluminium sebagai dudukan.',
                'description' => 'Pemasangan fasad bangunan menggunakan panel aluminium composite.',
                'status' => true,
            ],
        ];

        foreach ($finishedProducts as $fp) {
            FinishedProduct::updateOrCreate(['code' => $fp['code']], $fp);
        }

        $this->command->info('✅ Katalog fabrikasi (finished products) berhasil dibuat!');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'code' => 'WH-SMG-01',
                'name' => 'Gudang Utama Semarang',
                'manager' => 'Siti Rahma',
                'capacity' => 50000,
                'capacity_used' => 38500,
                'address' => 'Jl. Kaligawe Raya No.12, Semarang',
                'status' => true,
            ],
            [
                'code' => 'WH-JKT-02',
                'name' => 'Gudang Transit Jakarta',
                'manager' => 'Hendra Wijaya',
                'capacity' => 30000,
                'capacity_used' => 14200,
                'address' => 'Kawasan Industri Pulogadung Blok C, Jakarta',
                'status' => true,
            ],
            [
                'code' => 'WH-SBY-03',
                'name' => 'Gudang Transit Surabaya',
                'manager' => 'Ahmad Fauzi',
                'capacity' => 25000,
                'capacity_used' => 22800,
                'address' => 'Jl. Margomulyo No.45, Surabaya',
                'status' => true,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(['code' => $warehouse['code']], $warehouse);
        }
    }
}

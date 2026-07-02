<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class, // Must run first — creates roles & permissions
            WarehouseSeeder::class, // Harus sebelum UserSeeder (Mandor butuh warehouse_id)
            UserSeeder::class, // Creates demo users and assigns roles
            CategorySeeder::class,
            ProductSeeder::class, // Raw materials (kaca, aluminium, aksesoris)
            FinishedProductSeeder::class, // Katalog fabrikasi custom
            StockSeeder::class, // Stok hanya untuk raw material
            OrderSeeder::class, // Dummy orders + order items (raw_material & finished_product)
            DeliveryChecklistSeeder::class, // Dummy checklist 2-layer + foto
        ]);
    }
}

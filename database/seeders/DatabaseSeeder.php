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
            UserSeeder::class,           // Creates demo users and assigns roles
            WarehouseSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            StockSeeder::class,
        ]);
    }
}

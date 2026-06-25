<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================================
        // DEFINE ALL PERMISSIONS
        // ============================================================
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Warehouse
            'warehouse.view',
            'warehouse.create',
            'warehouse.edit',
            'warehouse.delete',

            // Category
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',

            // Product
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',

            // Inventory
            'inventory.view',
            'inventory.stockin',
            'inventory.stockout',
            'inventory.adjustment',

            // Delivery
            'delivery.view',
            'delivery.create',
            'delivery.approve',

            // Driver & GPS
            'driver.view',
            'driver.manage',
            'gps.view',

            // User & System
            'user.view',
            'user.manage',
            'audit.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ============================================================
        // DEFINE ROLES & ASSIGN PERMISSIONS
        // ============================================================

        // --- OWNER: Full Access ---
        $owner = Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'web']);
        $owner->syncPermissions(Permission::all());

        // --- ADMIN: Full minus User Management & Audit ---
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'dashboard.view',
            'warehouse.view', 'warehouse.create', 'warehouse.edit', 'warehouse.delete',
            'category.view', 'category.create', 'category.edit', 'category.delete',
            'product.view', 'product.create', 'product.edit', 'product.delete',
            'inventory.view', 'inventory.stockin', 'inventory.stockout', 'inventory.adjustment',
            'delivery.view', 'delivery.create', 'delivery.approve',
            'driver.view', 'driver.manage',
            'gps.view',
        ]);

        // --- STAFF GUDANG: Warehouse, Product, Inventory, QC & Loading ---
        $staffGudang = Role::firstOrCreate(['name' => 'Staff Gudang', 'guard_name' => 'web']);
        $staffGudang->syncPermissions([
            'dashboard.view',
            'warehouse.view',
            'product.view',
            'inventory.view', 'inventory.stockin', 'inventory.stockout', 'inventory.adjustment',
        ]);

        // --- DRIVER: GPS & Delivery ---
        $driver = Role::firstOrCreate(['name' => 'Driver', 'guard_name' => 'web']);
        $driver->syncPermissions([
            'dashboard.view',
            'delivery.view',
            'gps.view',
        ]);

        $this->command->info('✅ Roles dan Permissions berhasil dibuat!');
        $this->command->table(
            ['Role', 'Permissions'],
            [
                ['Owner', $owner->permissions->count() . ' permissions (full access)'],
                ['Admin', $admin->permissions->count() . ' permissions'],
                ['Staff Gudang', $staffGudang->permissions->count() . ' permissions'],
                ['Driver', $driver->permissions->count() . ' permissions'],
            ]
        );
    }
}

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

        // Bersihkan role lama "Admin" & "Staff Gudang" jika pernah ada di DB
        // (sisa dari eksperimen 7-role sebelum kembali ke konsep 5 role).
        // Users yang masih terpasang di role ini otomatis lepas rolenya;
        // pastikan re-assign manual lewat menu Manajemen User setelah seed.
        Role::whereIn('name', ['Admin', 'Staff Gudang'])->delete();

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
            'warehouse.gps.view',

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

            // Order
            'order.view',
            'order.create',
            'order.edit',
            'order.delete',
            'order.assign_driver',

            // Checklist (2-layer approval)
            'checklist.view',
            'checklist.create.mandor',
            'checklist.approve.mandor',
            'checklist.approve.kepala_lapangan',

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

        // --- KEPALA PRODUKSI (Pak Yudi): Monitoring & kelola lintas gudang.
        // Menyerap permission CRUD warehouse/category/product/order yang
        // sebelumnya dipegang role "Admin" (role tersebut dihapus, konsep
        // kembali ke 5 role sesuai dokumen awal).
        $kepalaProduksi = Role::firstOrCreate(['name' => 'Kepala Produksi', 'guard_name' => 'web']);
        $kepalaProduksi->syncPermissions(['dashboard.view', 'warehouse.view', 'warehouse.create', 'warehouse.edit', 'warehouse.delete', 'warehouse.gps.view', 'category.view', 'category.create', 'category.edit', 'category.delete', 'product.view', 'product.create', 'product.edit', 'product.delete', 'inventory.view', 'inventory.adjustment', 'order.view', 'order.create', 'order.edit', 'order.delete', 'checklist.view']);

        // --- MANDOR: Checklist layer 1 & stock movement di gudang sendiri.
        // Menyerap permission stock in/out yang sebelumnya dipegang role
        // "Staff Gudang" (role tersebut dihapus, digabung ke Mandor karena
        // Mandor memang sudah terikat 1 gudang lewat warehouse_id).
        $mandor = Role::firstOrCreate(['name' => 'Mandor', 'guard_name' => 'web']);
        $mandor->syncPermissions(['dashboard.view', 'warehouse.view', 'product.view', 'inventory.view', 'inventory.stockin', 'inventory.stockout', 'order.view', 'checklist.view', 'checklist.create.mandor', 'checklist.approve.mandor']);

        // --- KEPALA LAPANGAN (Pak Egi): Approval layer 2, koordinasi driver ---
        $kepalaLapangan = Role::firstOrCreate(['name' => 'Kepala Lapangan', 'guard_name' => 'web']);
        $kepalaLapangan->syncPermissions(['dashboard.view', 'warehouse.view', 'warehouse.gps.view', 'order.view', 'order.assign_driver', 'checklist.view', 'checklist.approve.kepala_lapangan', 'delivery.view', 'delivery.approve', 'driver.view', 'driver.manage', 'gps.view']);

        // --- DRIVER: GPS & Delivery ---
        $driver = Role::firstOrCreate(['name' => 'Driver', 'guard_name' => 'web']);
        $driver->syncPermissions(['dashboard.view', 'order.view', 'delivery.view', 'gps.view']);

        $this->command->info('✅ Roles dan Permissions berhasil dibuat! (5 role sesuai konsep awal)');
        $this->command->table(['Role', 'Permissions'], [['Owner', $owner->permissions->count() . ' permissions (full access)'], ['Kepala Produksi', $kepalaProduksi->permissions->count() . ' permissions'], ['Mandor', $mandor->permissions->count() . ' permissions'], ['Kepala Lapangan', $kepalaLapangan->permissions->count() . ' permissions'], ['Driver', $driver->permissions->count() . ' permissions']]);
    }
}

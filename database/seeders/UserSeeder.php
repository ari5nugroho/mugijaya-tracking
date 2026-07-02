<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil gudang yang sudah ada untuk dipasangkan ke Mandor
        $whSemarang = Warehouse::where('code', 'WH-SMG-01')->first();
        $whJakarta = Warehouse::where('code', 'WH-JKT-02')->first();
        $whSurabaya = Warehouse::where('code', 'WH-SBY-03')->first();

        $users = [
            [
                'name' => 'Ari Nugroho',
                'email' => 'owner@mugijaya.com',
                'password' => Hash::make('password'),
                'role' => 'Owner',
                'warehouse_id' => null,
            ],
            [
                'name' => 'Dian Prasetyo',
                'email' => 'driver@mugijaya.com',
                'password' => Hash::make('password'),
                'role' => 'Driver',
                'warehouse_id' => null,
            ],

            // --- Kepala Produksi: monitoring lintas gudang, tidak terikat 1 gudang ---
            [
                'name' => 'Yudi Hartono',
                'email' => 'kepalaproduksi@mugijaya.com',
                'password' => Hash::make('password'),
                'role' => 'Kepala Produksi',
                'warehouse_id' => null,
            ],

            // --- Kepala Lapangan: koordinasi driver & approval layer 2, tidak terikat 1 gudang ---
            [
                'name' => 'Egi Permana',
                'email' => 'kepalalapangan@mugijaya.com',
                'password' => Hash::make('password'),
                'role' => 'Kepala Lapangan',
                'warehouse_id' => null,
            ],

            // --- Mandor: terikat 1 gudang masing-masing ---
            [
                'name' => 'Joko Susilo',
                'email' => 'mandor.semarang@mugijaya.com',
                'password' => Hash::make('password'),
                'role' => 'Mandor',
                'warehouse_id' => $whSemarang?->id,
            ],
            [
                'name' => 'Rudi Hermawan',
                'email' => 'mandor.jakarta@mugijaya.com',
                'password' => Hash::make('password'),
                'role' => 'Mandor',
                'warehouse_id' => $whJakarta?->id,
            ],
            [
                'name' => 'Slamet Riyadi',
                'email' => 'mandor.surabaya@mugijaya.com',
                'password' => Hash::make('password'),
                'role' => 'Mandor',
                'warehouse_id' => $whSurabaya?->id,
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                    'email_verified_at' => now(),
                    'warehouse_id' => $data['warehouse_id'],
                ],
            );

            // Pastikan warehouse_id ikut terupdate kalau user sudah ada sebelumnya
            $user->update(['warehouse_id' => $data['warehouse_id']]);

            $user->syncRoles([$data['role']]);
        }

        $this->command->info('✅ Demo users berhasil dibuat!');
        $this->command->table(['Name', 'Email', 'Role', 'Warehouse ID', 'Password'], array_map(fn($u) => [$u['name'], $u['email'], $u['role'], $u['warehouse_id'] ?? '-', 'password'], $users));
    }
}

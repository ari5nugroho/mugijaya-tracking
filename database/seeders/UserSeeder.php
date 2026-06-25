<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Ari Nugroho',
                'email'    => 'owner@mugijaya.com',
                'password' => Hash::make('password'),
                'role'     => 'Owner',
            ],
            [
                'name'     => 'Budi Santoso',
                'email'    => 'admin@mugijaya.com',
                'password' => Hash::make('password'),
                'role'     => 'Admin',
            ],
            [
                'name'     => 'Citra Dewi',
                'email'    => 'staff@mugijaya.com',
                'password' => Hash::make('password'),
                'role'     => 'Staff Gudang',
            ],
            [
                'name'     => 'Dian Prasetyo',
                'email'    => 'driver@mugijaya.com',
                'password' => Hash::make('password'),
                'role'     => 'Driver',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => $data['password'],
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles([$data['role']]);
        }

        $this->command->info('✅ Demo users berhasil dibuat!');
        $this->command->table(
            ['Name', 'Email', 'Role', 'Password'],
            array_map(fn($u) => [$u['name'], $u['email'], $u['role'], 'password'], $users)
        );
    }
}

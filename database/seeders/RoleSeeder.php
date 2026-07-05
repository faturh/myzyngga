<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Zyngga',
                'slug' => 'admin',
                'email' => 'admin@zyngga.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
        $admin->assignRole('admin');

        // Create Customer
        $customer = User::updateOrCreate(
            ['username' => 'customer'],
            [
                'name' => 'Customer Zyngga',
                'slug' => 'customer',
                'email' => 'customer@zyngga.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );
        $customer->assignRole('customer');

        // Seed mock employees with different roles and salaries
        $cabangId = \App\Models\Cabang::where('nama', 'Cabang Pusat Pertama')->value('id') ?? 1;
        $employees = [
            [
                'username' => 'budi',
                'name' => 'Budi Gunawan',
                'slug' => 'budi-gunawan',
                'email' => 'budi@zyngga.com',
                'password' => Hash::make('password'),
                'role' => 'manajer_laundry',
                'phone' => '08123456780',
                'cabang_id' => $cabangId,
                'gaji' => 5000000,
            ],
            [
                'username' => 'siti',
                'name' => 'Siti Aminah',
                'slug' => 'siti-aminah',
                'email' => 'siti@zyngga.com',
                'password' => Hash::make('password'),
                'role' => 'pegawai_laundry',
                'phone' => '08123456781',
                'cabang_id' => $cabangId,
                'gaji' => 3500000,
            ],
            [
                'username' => 'andi',
                'name' => 'Andi Wijaya',
                'slug' => 'andi-wijaya',
                'email' => 'andi@zyngga.com',
                'password' => Hash::make('password'),
                'role' => 'pegawai_laundry',
                'phone' => '08123456782',
                'cabang_id' => $cabangId,
                'gaji' => 3200000,
            ],
            [
                'username' => 'joko',
                'name' => 'Joko Widodo',
                'slug' => 'joko-widodo',
                'email' => 'joko@zyngga.com',
                'password' => Hash::make('password'),
                'role' => 'gamis',
                'phone' => '08123456783',
                'cabang_id' => $cabangId,
                'gaji' => 2500000,
            ],
        ];

        foreach ($employees as $emp) {
            $user = User::updateOrCreate(
                ['username' => $emp['username']],
                [
                    'name' => $emp['name'],
                    'slug' => $emp['slug'],
                    'email' => $emp['email'],
                    'password' => $emp['password'],
                    'role' => $emp['role'],
                    'phone' => $emp['phone'],
                    'cabang_id' => $emp['cabang_id'],
                    'gaji' => $emp['gaji'],
                ]
            );
            $user->assignRole($emp['role']);
        }
    }
}

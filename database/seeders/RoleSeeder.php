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
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Zyngga',
                'slug' => 'admin',
                'email' => 'admin@zyngga.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Create Customer
        User::updateOrCreate(
            ['username' => 'customer'],
            [
                'name' => 'Customer Zyngga',
                'slug' => 'customer',
                'email' => 'customer@zyngga.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );
    }
}

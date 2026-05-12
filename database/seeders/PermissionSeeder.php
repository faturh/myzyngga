<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $roles = [
            'admin',
            'customer',
            'lurah',
            'pic',
            'rw',
            'manajer_laundry',
            'pegawai_laundry',
            'gamis',
            'guest'
        ];

        foreach ($roles as $role) {
            Role::findOrCreate($role);
        }
    }
}

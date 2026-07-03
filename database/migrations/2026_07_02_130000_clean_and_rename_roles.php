<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use App\Models\User;

return new class extends Migration {
    public function up(): void
    {
        // 1. Create operator role if not exists
        $operatorRole = Role::findOrCreate('operator');

        // 2. Re-assign role for users who have 'manajer_laundry'
        $users = User::all();
        foreach ($users as $user) {
            // Update raw role column
            if (in_array($user->role, ['manajer_laundry', 'lurah', 'pic', 'rw'])) {
                $user->role = 'operator';
                $user->saveQuietly();
            } elseif ($user->role === 'gamis') {
                $user->role = 'pegawai_laundry';
                $user->saveQuietly();
            }

            // Sync spatie roles
            if ($user->hasRole('manajer_laundry') || $user->hasRole('lurah') || $user->hasRole('pic') || $user->hasRole('rw')) {
                $user->removeRole('manajer_laundry');
                $user->removeRole('lurah');
                $user->removeRole('pic');
                $user->removeRole('rw');
                $user->assignRole('operator');
            }
            if ($user->hasRole('gamis')) {
                $user->removeRole('gamis');
                $user->assignRole('pegawai_laundry');
            }
        }

        // 3. Delete old roles
        Role::whereIn('name', ['manajer_laundry', 'lurah', 'pic', 'rw', 'gamis', 'guest'])->delete();
    }

    public function down(): void
    {
        // No-op or reverse if needed
    }
};

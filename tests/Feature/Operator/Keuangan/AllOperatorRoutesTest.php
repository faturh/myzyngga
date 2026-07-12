<?php

namespace Tests\Feature\Operator\Keuangan;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AllOperatorRoutesTest extends TestCase
{
    use DatabaseTransactions;

    private function createAdminUser(Cabang $cabang): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $user = User::factory()->create([
            'username' => 'admin-test-all',
            'slug' => 'admin-test-all',
            'email' => 'admin-test-all@example.com',
            'password' => 'password',
            'role' => 'admin',
            'cabang_id' => $cabang->id,
            'email_verified_at' => now(),
        ]);

        $user->assignRole('admin');

        return $user;
    }

    private function createManagerUser(Cabang $cabang): User
    {
        Role::firstOrCreate(['name' => 'manajer_laundry', 'guard_name' => 'web']);

        $user = User::factory()->create([
            'username' => 'manager-test-all',
            'slug' => 'manager-test-all',
            'email' => 'manager-test-all@example.com',
            'password' => 'password',
            'role' => 'manajer_laundry',
            'cabang_id' => $cabang->id,
            'email_verified_at' => now(),
        ]);

        $user->assignRole('manajer_laundry');

        return $user;
    }

    public function test_all_operator_routes(): void
    {
        $cabang = Cabang::query()->create([
            'nama' => 'Cabang Test All',
            'slug' => 'cabang-test-all',
            'lokasi' => 'Bandung',
            'alamat' => 'Jalan Ganesha No 10',
        ]);

        $admin = $this->createAdminUser($cabang);
        $manager = $this->createManagerUser($cabang);

        Role::firstOrCreate(['name' => 'pegawai_laundry', 'guard_name' => 'web']);

        $routes = [
            'Dashboard' => ['/admin/dashboard', 'admin'],
            'Riwayat Pesanan' => ['/admin/riwayat-pesanan', 'admin'],
            'Riwayat Pesanan Tambah Form' => ['/admin/riwayat-pesanan/tambah', 'admin'],
            'Gaji Karyawan' => ['/admin/gaji-karyawan', 'admin'],
            'Keuangan' => ['/admin/keuangan', 'admin'],
            'User List' => ['/user', 'admin'],
        ];

        foreach ($routes as $name => $info) {
            list($uri, $roleRequired) = $info;
            $userToUse = ($roleRequired === 'admin') ? $admin : $manager;
            
            $this->actingAs($userToUse);
            
            echo "\nTesting Route: $name ($uri) using $roleRequired -> ";
            $response = $this->get($uri);
            $status = $response->getStatusCode();
            echo "Status: $status";
            
            if ($status >= 400) {
                echo "\n  ERROR: " . substr($response->getContent(), 0, 500) . "\n";
            }
            
            $this->assertTrue(in_array($status, [200, 302]), "Route $name ($uri) failed with status $status");
        }
        echo "\n";
    }
}

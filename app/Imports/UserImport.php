<?php

namespace App\Imports;

use App\Models\Cabang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = User::withTrashed()->where('email', $row['email'])->first();
            if (empty($data)) {
                $cabang = Cabang::where('slug', $row['cabang'])->first();
                $user = User::create([
                    'email' => $row['email'],
                    'username' => $row['username'],
                    'password' => Hash::make($row['password']),
                    'cabang_id' => $cabang ? $cabang->id : null,
                    'name' => $row['nama_lengkap'] ?? null,
                ]);
                $user->assignRole($row['role']);
            }
        }
    }
}

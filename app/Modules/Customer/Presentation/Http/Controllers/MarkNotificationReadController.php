<?php

namespace App\Modules\Customer\Presentation\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\NotifikasiRead;
use App\Models\Pelanggan;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\Request;

class MarkNotificationReadController
{
    public function __invoke(Request $request, int $id)
    {
        $pelanggan = Pelanggan::where('user_id', $request->user()->id)->first();

        if (! $pelanggan) {
            return ApiResponse::error('Profil pelanggan tidak ditemukan.', 403);
        }

        $notifikasi = Notifikasi::find($id);

        if (! $notifikasi) {
            return ApiResponse::error('Notifikasi tidak ditemukan.', 404);
        }

        // IDOR protection: notifikasi personal hanya boleh ditandai oleh pemiliknya
        if ($notifikasi->pelanggan_id !== null && (int) $notifikasi->pelanggan_id !== $pelanggan->id) {
            return ApiResponse::error('Akses ditolak.', 403);
        }

        if ($notifikasi->pelanggan_id === null) {
            // Broadcast: catat per-pelanggan di notifikasi_reads
            NotifikasiRead::firstOrCreate(
                ['notifikasi_id' => $notifikasi->id, 'pelanggan_id' => $pelanggan->id],
                ['read_at' => now()],
            );
        } else {
            // Personal: tandai is_read langsung
            $notifikasi->update(['is_read' => true]);
        }

        return ApiResponse::success(['message' => 'Notifikasi ditandai sudah dibaca.']);
    }
}

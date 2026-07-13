<?php

namespace App\Modules\Customer\Presentation\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\NotifikasiRead;
use App\Models\Pelanggan;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\Request;

class ListNotificationsController
{
    public function __invoke(Request $request)
    {
        $pelanggan = Pelanggan::where('user_id', $request->user()->id)->first();

        if (! $pelanggan) {
            return ApiResponse::success(['notifications' => []]);
        }

        // 1. Notifikasi personal milik pelanggan ini
        $personal = Notifikasi::query()
            ->whereNotNull('pelanggan_id')
            ->where('pelanggan_id', $pelanggan->id)
            ->latest()
            ->get()
            ->map(fn (Notifikasi $n) => [
                'id'         => $n->id,
                'jenis'      => $n->jenis,
                'pesan'      => $n->pesan,
                'is_read'    => (bool) $n->is_read,
                'created_at' => $n->created_at?->toISOString(),
            ]);

        // 2. Notifikasi broadcast (pelanggan_id null), cek per-pelanggan via notifikasi_reads
        $broadcast = Notifikasi::query()
            ->whereNull('pelanggan_id')
            ->latest()
            ->get()
            ->map(fn (Notifikasi $n) => [
                'id'         => $n->id,
                'jenis'      => $n->jenis,
                'pesan'      => $n->pesan,
                'is_read'    => NotifikasiRead::where('notifikasi_id', $n->id)
                                    ->where('pelanggan_id', $pelanggan->id)
                                    ->exists(),
                'created_at' => $n->created_at?->toISOString(),
            ]);

        $all = $personal->concat($broadcast)
            ->sortByDesc('created_at')
            ->values();

        return ApiResponse::success(['notifications' => $all]);
    }
}

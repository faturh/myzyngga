<?php

namespace App\Modules\Order\Presentation\Http\Controllers;

use App\Models\Complaint;
use App\Models\Pelanggan;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\Request;

class ListComplaintsController
{
    public function __invoke(Request $request)
    {
        $pelanggan = Pelanggan::where('user_id', $request->user()->id)->first();

        if (! $pelanggan) {
            return ApiResponse::success(['complaints' => []]);
        }

        $complaints = Complaint::with('transaksi:id,nota,status')
            ->where('pelanggan_id', $pelanggan->id)
            ->latest()
            ->get()
            ->map(fn (Complaint $c) => [
                'id'          => $c->id,
                'transaksi_id' => $c->transaksi_id,
                'nota'        => optional($c->transaksi)->nota,
                'status_order' => optional($c->transaksi)->status,
                'content'     => $c->content,
                'status'      => $c->status,
                'issue_types' => $c->issue_types,
                'image_path'  => is_string($c->image_path) ? (json_decode($c->image_path, true) ?? []) : ($c->image_path ?? []),
                'created_at'  => $c->created_at?->toISOString(),
            ]);

        return ApiResponse::success(['complaints' => $complaints]);
    }
}

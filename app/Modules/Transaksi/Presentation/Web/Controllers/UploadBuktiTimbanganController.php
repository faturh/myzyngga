<?php

namespace App\Modules\Transaksi\Presentation\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Modules\Transaksi\Application\Services\UploadBuktiTimbanganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadBuktiTimbanganController extends Controller
{
    private UploadBuktiTimbanganService $service;

    public function __construct(UploadBuktiTimbanganService $service)
    {
        $this->service = $service;
    }

    public function upload(Request $request, Transaksi $transaksi)
    {
        $validator = Validator::make($request->all(), [
            'bukti_timbangan' => 'required|image|max:5120', // Max 5MB
        ], [
            'required' => ':attribute harus diisi.',
            'image' => ':attribute harus berupa gambar.',
            'max' => ':attribute tidak boleh lebih dari 5MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->with('error', 'Gagal mengunggah bukti timbangan.');
        }

        try {
            $this->service->uploadBuktiTimbangan($transaksi, $request->file('bukti_timbangan'));
            return redirect()->back()->with('success', 'Bukti timbangan berhasil diunggah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

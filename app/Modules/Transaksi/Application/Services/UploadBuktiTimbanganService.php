<?php

namespace App\Modules\Transaksi\Application\Services;

use App\Models\Transaksi;
use Illuminate\Http\UploadedFile;

class UploadBuktiTimbanganService
{
    public function uploadBuktiTimbangan(Transaksi $transaksi, UploadedFile $file): Transaksi
    {
        // Upload to Cloudinary and get the secure URL
        $path = $file->store('bukti-timbangan', 'cloudinary');
        $url = \Illuminate\Support\Facades\Storage::disk('cloudinary')->url($path);

        // Save URL to database
        $transaksi->update([
            'bukti_timbangan' => $url,
        ]);

        return $transaksi;
    }
}

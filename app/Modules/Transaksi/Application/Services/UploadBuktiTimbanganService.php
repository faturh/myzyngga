<?php

namespace App\Modules\Transaksi\Application\Services;

use App\Models\Transaksi;
use Illuminate\Http\UploadedFile;

class UploadBuktiTimbanganService
{
    public function uploadBuktiTimbangan(Transaksi $transaksi, UploadedFile $file): Transaksi
    {
        // Upload to Cloudinary and get the secure URL
        $url = $file->storeOnCloudinary('bukti-timbangan')->getSecurePath();

        // Save URL to database
        $transaksi->update([
            'bukti_timbangan' => $url,
        ]);

        return $transaksi;
    }
}

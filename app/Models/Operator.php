<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    /**
     * Get count of orders that need to be processed (status 'Baru' or 'created').
     */
    public static function getPerluDiprosesCount(): int
    {
        return Transaksi::whereIn('status', ['Baru', 'created'])->count();
    }

    /**
     * Get count of orders waiting for payment (status 'Proses' and payment_status 'pending').
     */
    public static function getMenungguPembayaranCount(): int
    {
        return Transaksi::where('status', 'Proses')
            ->where('payment_status', 'pending')
            ->count();
    }

    /**
     * Get count of orders that need to be worked on (status 'Proses' and payment_status 'paid').
     */
    public static function getPerluDikerjakanCount(): int
    {
        return Transaksi::where('status', 'Proses')
            ->where('payment_status', 'paid')
            ->count();
    }

    /**
     * Get count of completed orders (status 'Selesai').
     */
    public static function getPesananSelesaiCount(): int
    {
        return Transaksi::where('status', 'Selesai')->count();
    }
}

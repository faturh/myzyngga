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
        return Transaksi::where('list_status_pengerjaan_id', 1)->count();
    }

    /**
     * Get count of orders waiting for payment (status 'Proses' and payment_status 'pending').
     */
    public static function getMenungguPembayaranCount(): int
    {
        return Transaksi::where('list_status_pengerjaan_id', 2)->count();
    }

    /**
     * Get count of orders that need to be worked on (status 'Proses' and payment_status 'paid').
     */
    public static function getPerluDikerjakanCount(): int
    {
        return Transaksi::where('list_status_pengerjaan_id', 3)->count();
    }

    /**
     * Get count of completed orders (status 'Selesai').
     */
    public static function getPesananSelesaiCount(): int
    {
        return Transaksi::where('list_status_pengerjaan_id', 5)->count();
    }

    /**
     * Get count of orders in progress (status 'Siap Ambil', 'Antar', or 'Jemput').
     */
    public static function getProsesPengerjaanCount(): int
    {
        return Transaksi::where('list_status_pengerjaan_id', 4)->count();
    }

    /**
     * Get count of orders with obstacle (status 'Kendala Pesanan').
     */
    public static function getKendalaPesananCount(): int
    {
        return Transaksi::where('list_status_pengerjaan_id', 6)->count();
    }

    /**
     * Get count of cancelled orders (status 'Sedang Dibatalkan').
     */
    public static function getSedangDibatalkanCount(): int
    {
        return Transaksi::where('list_status_pengerjaan_id', 7)->count();
    }

    /**
     * Get count of orders being picked up (status 'Sedang Dijemput').
     */
    public static function getSedangDijemputCount(): int
    {
        return Transaksi::where('list_status_pengerjaan_id', 8)->count();
    }
}

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Biaya Pengantaran (Delivery Fee)
    |--------------------------------------------------------------------------
    | Tarif tetap pengantaran per transaksi dalam Rupiah.
    | Nilai ini ditetapkan di server — TIDAK boleh dikirim dari client.
    | Ubah via environment variable LAUNDRY_DELIVERY_FEE.
    |
    */
    'delivery_fee' => (int) env('LAUNDRY_DELIVERY_FEE', 5000),
];

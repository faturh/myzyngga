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
    | Default 0 karena pengantaran memang gratis (lihat Syarat & Ketentuan
    | "Gratis pickup dan delivery" di halaman detail pesanan pelanggan).
    | Sebelumnya default 5000 di sini diam-diam menambah Total pesanan yang
    | sudah Lunas tanpa update status pembayaran maupun cara untuk melunasinya.
    |
    */
    'delivery_fee' => (int) env('LAUNDRY_DELIVERY_FEE', 0),
];

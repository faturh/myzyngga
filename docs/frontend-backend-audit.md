# Audit Kesesuaian Frontend dan Backend

Tanggal audit: 2026-05-19

## Ringkasan

- Migration dan seeder ke Neon sudah berjalan.
- Tabel dan data awal utama sudah tersedia di Neon.
- Fokus implementasi saat ini dibatasi ke backend pelanggan sesuai instruksi terbaru.
- Frontend pelanggan utama sudah disambungkan ke data order real.
- Frontend operator/admin lama tetap dicatat sebagai temuan audit, tetapi tidak diubah.

## Status Data Neon

Data awal setelah migration dan seeder:

- `users`: 17
- `pelanggan`: 10
- `transaksi`: 150
- `payments`: 0

Dampak:

- Login dan data dasar demo sudah tersedia.
- Flow buat pesanan sudah memiliki data `cabang`, `layanan_prioritas`, dan petugas.
- Payment record baru akan terbentuk saat pelanggan membuat order baru atau pembayaran diverifikasi.

## Frontend Pelanggan

### Sudah Ada Backend

- Login/register/logout:
  - Route: `login`, `register`, `logout`
  - View: `resources/views/livewire/pages/auth/**`

- Dashboard pelanggan:
  - Route: `dashboard`
  - Controller: `App\Modules\Customer\Presentation\Web\Controllers\CustomerDashboardController`
  - Catatan: kartu pesanan aktif dan pesanan terakhir sudah memakai transaksi pelanggan login.

- Profile pelanggan:
  - Route: `profile`, `profile.account`
  - View langsung ke `pelanggan.profile.index` dan `pelanggan.profile.account`

- CRUD alamat pelanggan:
  - Route: `addresses.create`, `addresses.store`, `addresses.edit`, `addresses.update`, `addresses.destroy`, `addresses.primary`
  - Controller: `app/Http/Controllers/AddressController.php`

- Flow order pickup dan booking:
  - Route: `order.pickup`, `order.pickup.store`, `order.pickup.details`, `order.pickup.details.store`, `order.booking`, `order.confirm`, `order.cancel`
  - Controller: `App\Modules\Order\Presentation\Web\Controllers\OrderPageController`
  - Service: `App\Modules\Order\Application\Services\OrderWebService`

- API customer/order/payment:
  - `GET api/v1/customer/profile`
  - `PUT api/v1/customer/address`
  - `PUT api/v1/customer/preferences`
  - `POST api/v1/orders`
  - `GET api/v1/orders/history`
  - `GET api/v1/orders/{orderId}`
  - `PATCH api/v1/orders/{orderId}/status`
  - `GET api/v1/payment/methods`
  - `POST api/v1/payments/{orderId}/verify`

### Sudah Disambungkan ke Data Real

- Dashboard pelanggan:
  - File: `resources/views/pelanggan/dashboard/index.blade.php`
  - Status: memakai `OrderWebService::dashboardData()`.

- Riwayat pesanan:
  - Route ada: `order.history`
  - File: `resources/views/pelanggan/order/history.blade.php`
  - Status: memakai transaksi pelanggan login dari repository order.

- Detail pesanan:
  - Route ada: `order.detail`
  - File: `app/Modules/Order/Presentation/Web/Controllers/OrderPageController.php`
  - Status: method `detail()` mengambil transaksi real dan memetakan data di application service.

- Cek pesanan publik:
  - Route ada: `order.check`
  - File: `app/Modules/Order/Application/Services/OrderWebService.php`
  - Status: pencarian memakai DB berdasarkan ID/nota/nama dan verifikasi 4 digit terakhir nomor WhatsApp.

- Notifikasi:
  - Route ada: `notifications`
  - File: `resources/views/pelanggan/notifications/index.blade.php`
  - Status: notifikasi dimapping dari status transaksi pelanggan.

## Frontend Operator/Admin

### Route Operator yang Sudah Ada

Route operator yang saat ini aktif terutama hanya transaksi:

- `admin.dashboard`
- `transaksi`
- `transaksi.jadwal`
- `transaksi.view`
- `transaksi.create`
- `transaksi.store`
- `transaksi.edit`
- `transaksi.update`
- `transaksi.delete`
- `transaksi.edit.status`
- `transaksi.update.status`
- `transaksi.create.ubahJenisPakaian`
- `transaksi.create.ubahJenisLayanan`
- `transaksi.create.ubahLayananTambahan`
- `transaksi.create.hitungTotalBayar`
- `transaksi.cetak-struk`
- `transaksi.konfirmasiUpah`
- `transaksi.lurah`
- `transaksi.lurah.cabang`
- `transaksi.lurah.cabang.jadwal`
- `transaksi.lurah.view`
- `transaksi.lurah.cabang.create`
- `transaksi.lurah.cabang.store`
- `transaksi.lurah.cabang.edit`
- `transaksi.lurah.cabang.update`
- `transaksi.lurah.cabang.delete`
- `transaksi.lurah.cabang.edit.status`
- `transaksi.lurah.cabang.update.status`
- `transaksi-gamis`
- `transaksi-gamis.semua`
- `transaksi-gamis.view`
- `transaksi-gamis.view.layanan`

### Route Operator yang Dipakai Frontend tetapi Belum Terdaftar

Catatan: sesuai instruksi terbaru, area operator/admin tidak diubah pada implementasi ini. Daftar berikut tetap menjadi temuan audit untuk workstream operator, bukan scope backend pelanggan.

Kelompok data master:

- `cabang`
- `cabang.show`
- `cabang.store`
- `cabang.edit`
- `cabang.update`
- `cabang.delete`
- `cabang.restore`
- `cabang.destroy`
- `umr`
- `umr.show`
- `umr.store`
- `umr.edit`
- `umr.update`
- `umr.delete`

Kelompok user management:

- `user`
- `user.create`
- `user.store`
- `user.view`
- `user.edit`
- `user.update`
- `user.edit.password`
- `user.update.password`
- `user.delete`
- `user.restore`
- `user.destroy`
- `user.trash`
- `user.import`
- `user.export`
- `user.cabang`
- `user.cabang.create`
- `rw`
- `rw.create`
- `rw.store`
- `rw.view`
- `rw.edit`
- `rw.update`
- `rw.edit.password`
- `rw.update.password`
- `rw.delete`
- `rw.restore`
- `rw.destroy`
- `rw.trash`
- `rw.import`
- `rw.export`
- `gamis`
- `gamis.show`
- `gamis.store`
- `gamis.edit`
- `gamis.update`
- `gamis.delete`
- `gamis.import`
- `gamis.anggota`
- `gamis.anggota.show`

Kelompok pelanggan operator:

- `pelanggan`
- `pelanggan.show`
- `pelanggan.store`
- `pelanggan.edit`
- `pelanggan.update`
- `pelanggan.delete`
- `pelanggan.import`
- `pelanggan.export`

Kelompok layanan:

- `layanan-cabang`
- `layanan-cabang.cabang`
- `layanan-cabang.trash`
- `jenis-layanan`
- `jenis-layanan.show`
- `jenis-layanan.store`
- `jenis-layanan.edit`
- `jenis-layanan.update`
- `jenis-layanan.delete`
- `jenis-layanan.restore`
- `jenis-layanan.destroy`
- `jenis-layanan.import`
- `jenis-layanan.export`
- `jenis-pakaian`
- `jenis-pakaian.show`
- `jenis-pakaian.store`
- `jenis-pakaian.edit`
- `jenis-pakaian.update`
- `jenis-pakaian.delete`
- `jenis-pakaian.restore`
- `jenis-pakaian.destroy`
- `jenis-pakaian.import`
- `jenis-pakaian.export`
- `harga-jenis-layanan`
- `harga-jenis-layanan.show`
- `harga-jenis-layanan.store`
- `harga-jenis-layanan.edit`
- `harga-jenis-layanan.update`
- `harga-jenis-layanan.delete`
- `harga-jenis-layanan.restore`
- `harga-jenis-layanan.destroy`
- `harga-jenis-layanan.import`
- `harga-jenis-layanan.export`
- `layanan-prioritas`
- `layanan-prioritas.show`
- `layanan-prioritas.store`
- `layanan-prioritas.edit`
- `layanan-prioritas.update`
- `layanan-prioritas.delete`
- `layanan-prioritas.restore`
- `layanan-prioritas.destroy`
- `layanan-prioritas.import`
- `layanan-prioritas.export`
- `layanan-tambahan`
- `layanan-tambahan.show`
- `layanan-tambahan.store`
- `layanan-tambahan.edit`
- `layanan-tambahan.update`
- `layanan-tambahan.delete`
- `layanan-tambahan.restore`
- `layanan-tambahan.destroy`
- `layanan-tambahan.import`
- `layanan-tambahan.export`

Kelompok monitoring dan laporan:

- `monitoring`
- `monitoring.update.data`
- `monitoring.reset.data`
- `monitoring.edit.pemasukkan`
- `monitoring.update.pemasukkan`
- `monitoring.gamis.riwayat`
- `monitoring.rw`
- `monitoring.rw.pdf`
- `laporan.pendapatan.laundry`
- `laporan.pendapatan.laundry.pdf`
- `laporan.pendapatan.gamis`
- `laporan.pendapatan.gamis.pdf`
- `laporan.pelanggan`
- `laporan.pelanggan.pdf`
- `laporan.gamis`
- `laporan.gamis.pdf`

Kelompok lain:

- `profile.edit`
- `profile.update`
- `profile.destroy`
- `profile.edit.password`
- `profile.update.password`
- `transaksi.lurah.cabang.konfirmasiUpah`

## Hardcoded / Dummy Data yang Masih Ada

### Pelanggan

- `resources/views/pelanggan/order/booking.blade.php`
  - Pilihan layanan dan metode pembayaran masih berupa katalog lokal di Blade.
  - Catatan: flow submit sudah masuk backend dan tersimpan ke transaksi; katalog dinamis layanan/payment bisa menjadi iterasi berikutnya jika tabel kontraknya ingin dipakai sebagai sumber tunggal.

Hardcoded dummy order pelanggan yang sudah dihapus:

- ID dummy `IJK902H8MAHD`
- nama dummy `Rafi Syihan`
- alamat dummy `Telkom University`
- total dummy `Rp33.000`
- placeholder pencarian `rafi/ZYG-12345`

### Seeders

Seeder memang berisi data contoh. Ini normal untuk development/demo, tetapi jangan dianggap data produksi:

- `database/seeders/akun/**`
- `database/seeders/layanan/**`
- `database/seeders/TransaksiSeeder.php`
- `database/seeders/TransaksiSuksesSeeder.php`

## Prioritas Pengerjaan

1. Iterasi opsional backend pelanggan:
   - jadikan katalog layanan booking bersumber dari tabel layanan/harga.
   - jadikan metode pembayaran bersumber dari konfigurasi/tabel resmi jika dibutuhkan.
   - tambah test feature untuk dashboard/history/detail/check order pelanggan.

2. Workstream operator terpisah:
   - route dan controller operator yang belum lengkap tetap perlu dikerjakan oleh scope operator/admin.
   - tidak ada perubahan operator/admin pada implementasi backend pelanggan ini.

# MyZyngga - Sistem Informasi Laundry

Aplikasi web berbasis Laravel untuk manajemen laundry terintegrasi dengan program GAMIS (Gabungan Masyarakat Industri Skala), mencakup multi-cabang, manajemen transaksi, laporan pendapatan, dan monitoring program sosial.

---

## Persyaratan Sistem

- PHP >= 8.2
- Composer
- MySQL / MariaDB
- Node.js & NPM
- Laravel 11

---

## Cara Menjalankan Project

### 1. Clone Repository

```bash
git clone https://github.com/faturh/myzyngga.git
cd myzyngga
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env`, sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=MyZyngga
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Buat Database

Buat database baru di MySQL/MariaDB dengan nama `MyZyngga` (atau sesuaikan dengan `.env`).

### 5. Jalankan Migrasi & Seeder

```bash
php artisan migrate:fresh --seed
```

### 6. Jalankan Server

Buka **dua terminal** secara bersamaan:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Vite (Assets):**
```bash
npm run dev
```

### 7. Akses Aplikasi

Buka browser dan akses: **http://127.0.0.1:8000**

---

## Akun Default (Setelah Seeder)

Semua akun menggunakan password: **`password`**

| Role | Email | Keterangan |
|------|-------|------------|
| **Lurah** | lurah@gmail.com | Admin utama, akses penuh |
| **PIC** | pic@gmail.com | Person in Charge, akses hampir sama dengan Lurah |
| **RW** | rw@gmail.com | Ketua RW, monitoring program GAMIS |
| **Manajer Laundry** | manajer@gmail.com | Manajer cabang laundry |
| **Pegawai Laundry** | pegawai@gmail.com | Pegawai operasional laundry |
| **GAMIS** | gamis1@gmail.com | Anggota program GAMIS |

---

## Alur / Flow Aplikasi

### Halaman Publik (Tanpa Login)

- **`/`** - Landing page berisi informasi layanan laundry
- **`/nota`** - Cek status transaksi berdasarkan nomor nota (tanpa login)

---

### Alur per Role

#### Lurah & PIC (Admin Utama)

| Menu | Fungsi |
|------|--------|
| Cabang | Kelola data cabang laundry (tambah, ubah, hapus, restore) |
| UMR | Atur standar UMR untuk perhitungan upah GAMIS |
| User | Kelola semua akun pengguna sistem |
| RW | Kelola akun Ketua RW (import/export Excel) |
| Jenis Layanan | Kelola jenis layanan cuci (reguler, express, dll) |
| Jenis Pakaian | Kelola kategori pakaian (kaos, gamis, celana, dll) |
| Harga Jenis Layanan | Atur harga per jenis layanan dan pakaian |
| Layanan Prioritas | Atur layanan prioritas pelanggan |
| Layanan Tambahan | Kelola layanan tambahan (setrika, parfum, dll) |
| Pelanggan | Manajemen data pelanggan (import/export Excel) |
| GAMIS | Kelola data keluarga GAMIS beserta anggotanya |
| Transaksi | Lihat & kelola transaksi semua cabang |
| Monitoring GAMIS | Monitor pendapatan & program GAMIS |
| Laporan | Cetak laporan pendapatan laundry, GAMIS, pelanggan (PDF) |

#### Manajer Laundry

| Menu | Fungsi |
|------|--------|
| User | Kelola pegawai di cabang sendiri |
| Transaksi | Kelola transaksi cabang sendiri |
| Pelanggan | Kelola data pelanggan cabang |
| Monitoring GAMIS | Monitor program GAMIS di cabang |
| Laporan | Laporan pendapatan cabang |

#### Pegawai Laundry

| Menu | Fungsi |
|------|--------|
| Transaksi | Input transaksi baru & update status laundry |
| Pelanggan | Tambah & lihat data pelanggan |

#### RW (Ketua RW)

| Menu | Fungsi |
|------|--------|
| Monitoring GAMIS | Lihat data GAMIS di wilayah RW, cetak laporan PDF |

#### GAMIS (Anggota Program)

| Menu | Fungsi |
|------|--------|
| Transaksi Gamis | Lihat transaksi harian & riwayat transaksi yang dikerjakan |

---

### Alur Transaksi Laundry

```
Pelanggan datang
    Pegawai/Manajer input Transaksi Baru
    (pilih pelanggan, jenis pakaian, jenis layanan, layanan tambahan)
    Sistem hitung total bayar otomatis
    Status: MENUNGGU
    Status: DIPROSES (GAMIS mulai mengerjakan)
    Status: SELESAI
    Cetak Struk / Nota
    Pelanggan bisa cek status via /nota (tanpa login)
```

---

### Alur Program GAMIS

```
Lurah/PIC input data Keluarga GAMIS
    Tambah anggota GAMIS (beserta pemasukkan lain)
    Transaksi laundry dikerjakan oleh GAMIS
    Lurah/Manajer konfirmasi upah GAMIS per transaksi
    Monitoring GAMIS: hitung total pendapatan vs UMR
    RW bisa lihat & cetak laporan monitoring wilayahnya
    Laporan pendapatan GAMIS dicetak (PDF)
```

---

## Fitur Tambahan

- **Import/Export Excel** - Data user, RW, jenis layanan, jenis pakaian, harga, pelanggan
- **Soft Delete** - Data yang dihapus bisa dipulihkan dari trash
- **Cetak Struk** - Cetak struk transaksi langsung dari sistem
- **Laporan PDF** - Laporan pendapatan laundry, GAMIS, pelanggan, dan detail GAMIS
- **Multi Cabang** - Sistem mendukung beberapa cabang laundry sekaligus
- **Role-based Access** - 6 level akses berbeda (Lurah, PIC, RW, Manajer, Pegawai, GAMIS)

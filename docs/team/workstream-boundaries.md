# Team Workstream Boundaries

Dokumen ini wajib dibaca agent sebelum menyentuh struktur folder, route, view, atau module.

## Tujuan

Project ini dikerjakan oleh 4 stream:

- Frontend Pelanggan
- Backend Pelanggan
- Frontend Operator
- Backend Operator

Semua perubahan harus masuk ke area yang tepat agar tidak saling tabrak.

## Peta Folder Resmi

### 1. Frontend Pelanggan

- `resources/views/pelanggan/**`
- halaman dashboard pelanggan ada di `resources/views/pelanggan/dashboard/**`
- halaman order pelanggan ada di `resources/views/pelanggan/order/**`
- halaman profile pelanggan sederhana ada di `resources/views/pelanggan/profile/**`

### 2. Backend Pelanggan

- route web pelanggan: `routes/web/pelanggan.php`
- route API pelanggan: `routes/api/pelanggan.php` dan `routes/api/pelanggan/**`
- module utama:
  - `app/Modules/Auth`
  - `app/Modules/Customer`
  - `app/Modules/Order`
  - `app/Modules/Payment`

### 3. Frontend Operator

- `resources/views/operator/**`
- dashboard/operator legacy yang masih aktif ada di `resources/views/operator/dashboard/**`
- halaman admin sederhana ada di `resources/views/operator/admin/**`

### 4. Backend Operator

- route web operator: `routes/web/operator.php`
- route API operator: `routes/api/operator.php` dan `routes/api/operator/**`
- module utama:
  - `app/Modules/Admin`
  - `app/Modules/Transaksi`
- legacy operator controller yang masih hidup ada di `app/Http/Controllers/**`, tetapi hanya boleh dirapikan sebagai adapter tipis

## Aturan Pemilahan Kerja

- Jika task user menyentuh dashboard customer, pickup, booking, history, payment customer, atau profile customer:
  - kerjakan di area `pelanggan`
- Jika task user menyentuh dashboard admin, transaksi operator, laporan, cabang, user staff, monitoring, atau gamis:
  - kerjakan di area `operator`
- Jangan menaruh view pelanggan ke folder operator.
- Jangan menaruh view operator ke folder pelanggan.
- Jangan menaruh route pelanggan di file route operator.
- Jangan menaruh route operator di file route pelanggan.

## Aturan DDD Per Stream

- Backend pelanggan dan backend operator sama-sama wajib mengikuti DDD modular.
- Presentation tetap tipis.
- Application tidak boleh query Eloquent langsung.
- Infrastructure adalah tempat query dan integrasi eksternal.
- Jika masih ada controller legacy operator, jangan tambah business logic baru di sana.

## Aturan Naming

- Gunakan istilah `pelanggan` untuk customer-facing area.
- Gunakan istilah `operator` untuk admin/operator-facing area.
- Jangan campur istilah `admin`, `operator`, `customer`, dan `pelanggan` tanpa alasan.
- Untuk route name publik yang sudah dipakai aplikasi, backward compatibility boleh dipertahankan, tetapi folder fisik dan file ownership tetap harus jelas.

## Aturan Branch Dan Merge

Untuk perubahan fitur pelanggan, alur default yang wajib dipakai:

1. commit ke `Backend-Pelanggan`
2. push ke `origin/Backend-Pelanggan`
3. merge ke `Finalisasi(Sebelum-merge-main)`
4. push ke `origin/Finalisasi(Sebelum-merge-main)`
5. merge ke `main`
6. push ke `origin/main`

Jika perubahan menyentuh operator dan pelanggan sekaligus:

- tetap sebutkan scope campurannya dengan jelas di commit message dan ringkasan kerja
- jangan lompat langsung ke `main`
- tetap lewat `Finalisasi(Sebelum-merge-main)`

## Checklist Sebelum Selesai

- folder view berada di stream yang benar
- route file berada di stream yang benar
- module backend berada di domain yang benar
- instruksi di `AGENTS.md` dan dokumen arsitektur ikut sinkron jika boundary berubah
- test relevan dijalankan

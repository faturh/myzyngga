# Backend DDD Guardrails

Dokumen ini adalah jalur wajib untuk agent sebelum menyentuh backend.

## 1. Target Arsitektur

Backend Zyngga harus bergerak ke DDD modular dengan struktur berikut:

- `app/Modules/<Module>/Presentation`
- `app/Modules/<Module>/Application`
- `app/Modules/<Module>/Domain`
- `app/Modules/<Module>/Infrastructure`
- `app/Shared` untuk concern lintas module

Setiap perubahan backend baru harus masuk ke struktur ini. Namespace lama di luar pola tersebut dianggap legacy.

## 2. Aturan Layer

### Presentation

- Berisi controller, request, resource, presenter, atau web action.
- Hanya boleh mengurus HTTP, auth, validation, serialization, dan delegasi ke application service.
- Tidak boleh berisi query database, kalkulasi bisnis, transaksi DB, atau keputusan domain yang kompleks.

### Application

- Berisi use case dan orchestration.
- Boleh menggabungkan beberapa repository/interface.
- Tidak boleh memanggil `Model::query()`, `DB::table()`, atau facade infrastruktur lain secara langsung kecuali benar-benar cross-cutting dan tidak ada abstraksi yang lebih tepat.
- Semua dependency persistence harus lewat interface.

### Domain

- Berisi kontrak repository, aturan domain, value object, enum domain, dan exception domain.
- Tidak boleh bergantung pada request, controller, resource, blade, atau session.

### Infrastructure

- Implementasi repository berbasis Eloquent, query builder, external API client, cache, queue, storage, dan detail framework lainnya.
- Tempat yang valid untuk menyentuh `App\Models\*`.

## 3. Working Agreement

- Jangan tambah fitur backend baru di `app/Http/Controllers` atau service legacy lain.
- Jika file legacy harus disentuh, ubah menjadi wrapper tipis yang mendelegasikan ke module.
- Hindari static helper yang menyembunyikan dependency penting.
- Gunakan DTO atau payload object untuk input use case yang cukup kompleks.
- Pakai resource/response envelope yang konsisten untuk API.
- Gunakan nama module berdasarkan capability bisnis, bukan berdasarkan jenis file.

## 3A. Mandatory Execution Flow For Agents

Sebelum agent mengubah backend, urutannya wajib seperti ini:

1. Baca `AGENTS.md`.
2. Baca dokumen ini.
3. Audit jalur eksekusi aktif:
   - route terkait
   - controller atau action terkait
   - application service terkait
   - repository interface dan implementasinya
4. Identifikasi pelanggaran layer yang sudah ada.
5. Pilih strategi perubahan:
   - lanjutkan module yang sudah ada, atau
   - buat module baru jika capability memang belum punya home yang tepat
6. Baru lakukan implementasi.

Jika agent melewati urutan ini, maka implementasi dianggap tidak patuh arsitektur.

## 3B. Routing Rules

- Route API baru wajib mengarah ke action/controller di `Presentation/Http`.
- Route web baru wajib mengarah ke action/controller di `Presentation/Web`.
- Hindari closure route untuk use case bisnis kecuali benar-benar statis dan tidak punya orchestration.
- Jangan daftarkan route baru ke controller legacy jika module yang benar sudah ada atau bisa dibuat.

## 3C. Request And Response Rules

- Validasi request HTTP pakai Form Request untuk endpoint baru atau endpoint yang sedang dirapikan.
- Resource/transformer dipakai untuk response API agar kontrak stabil.
- Untuk web flow, controller cukup memilih view dan payload yang sudah disiapkan service.
- Error domain dikonversi di boundary presentation, bukan dilempar acak dari view atau route closure.

## 3D. Persistence Rules

- Interface repository hidup di `Domain/Repositories`.
- Implementasi repository hidup di `Infrastructure/Persistence`.
- Query yang panjang, join kompleks, agregasi, dan lookup option harus tinggal di repository, bukan di application service.
- Application service boleh mengatur transaksi lintas repository, tetapi jangan menaruh detail query di sana.

## 3E. Legacy Migration Rules

- Saat task menyentuh file legacy besar, migrasikan secara bertahap.
- Prioritas migrasi:
  - query baca
  - query tulis
  - validasi request
  - pemetaan response
- Jika belum sempat selesai penuh, minimal hentikan pertumbuhan technical debt: jangan tambahkan logic baru ke file legacy itu.

## 4. Definition Of Clean Code Di Repo Ini

- Satu class punya satu alasan utama untuk berubah.
- Nama method harus menjelaskan intent bisnis, bukan detail teknis.
- Tidak ada query database tersebar di controller.
- Tidak ada hardcoded fallback yang diam-diam mengikat ke data tertentu tanpa validasi.
- Error domain harus eksplisit dan bermakna.
- Perubahan harus menjaga backward compatibility route/response jika user tidak meminta breaking change.

## 5. Audit Status Saat Ini

Jalur backend aktif saat ini sudah mengarah ke modular DDD:

- `routes/api.php` dan file turunan `routes/api/*.php`
- `routes/web.php`
- module aktif: `Admin`, `Auth`, `Customer`, `Order`, `Payment`

Area yang masih legacy atau belum sepenuhnya DDD:

- `app/Http/Controllers/*` lama
- `app/Modules/Transaksi/Application/Services/TransaksiDashboardService.php`
- file lain yang masih melakukan query Eloquent langsung di luar layer infrastructure

Progress terbaru:

- module `Order` sudah dipindahkan agar orchestration utama tidak query model langsung
- module `Payment` sudah memakai repository untuk lookup dan update order/payment
- module `Transaksi` mulai dipindahkan untuk query baca ke repository, tetapi write path dan validasi manual masih perlu dirapikan lebih lanjut

Aturan tegas:

- Jangan tambah logic baru di area legacy tersebut.
- Jika sebuah prompt menyentuh area legacy, migrasikan logic ke module terlebih dahulu atau minimal jadikan file legacy sebagai adapter tipis.

## 6. Checklist Sebelum Commit

- Perubahan sudah ditempatkan di module yang benar.
- Controller/request tetap tipis.
- Application service tidak query model langsung.
- Repository interface dan implementasinya sinkron.
- Test relevan dijalankan.
- Dokumen ini dan `AGENTS.md` diperbarui jika standar repo berubah.

## 7. Definition Of Professional Backend In This Repo

Backend dianggap profesional jika:

- struktur module mudah dipahami dari nama folder
- dependency flow satu arah: Presentation -> Application -> Domain, dan Infrastructure mengimplementasikan Domain contract
- use case punya boundary jelas
- error handling konsisten
- tidak ada hidden coupling ke data dummy atau ID hardcoded
- perubahan mudah dites tanpa harus menembus seluruh framework
- dokumentasi arsitektur ikut hidup bersama kode

# AI Agent Operating Instructions

Semua agent wajib membaca file ini sebelum membuat perubahan apa pun di repo ini.

## Fokus Utama

- Backend harus mengikuti DDD modular yang berpusat di `app/Modules`.
- Jangan menambah business logic baru ke namespace legacy seperti `app/Http/Controllers`, `app/Models` consumer langsung, atau service lama yang belum dimodularisasi.
- Jika ada kebutuhan backend baru, buat atau lanjutkan di module yang relevan dengan alur `Presentation -> Application -> Domain -> Infrastructure`.

## Sumber Aturan

- Baca dulu `docs/architecture/backend-ddd-guardrails.md` sebelum implementasi backend.
- Jika instruksi user bertentangan dengan guardrail ini, berhenti dan jelaskan trade-off terlebih dahulu. Jangan langsung menembus aturan arsitektur.

## Aturan Eksekusi

- Controller, route action, dan request class harus tipis: validasi, otorisasi, mapping request/response saja.
- Application service tidak boleh melakukan query Eloquent statis langsung. Gunakan repository interface dari layer domain.
- Infrastructure adalah satu-satunya layer yang boleh menyentuh Eloquent, query builder, DB facade, filesystem, HTTP client, dan integrasi eksternal.
- Domain tidak boleh bergantung ke controller, request, response, atau facade Laravel.
- Saat menyentuh kode legacy, ubah menjadi adapter tipis atau migrasikan logikanya ke module. Jangan menambah hutang teknis baru di area legacy.

## Workflow Wajib Sebelum Coding

- Baca `AGENTS.md` ini sampai selesai.
- Baca `docs/architecture/backend-ddd-guardrails.md`.
- Jika task menyentuh backend, audit dulu route, controller, service, dan repository yang aktif sebelum mengubah file.
- Tentukan apakah perubahan termasuk `Presentation`, `Application`, `Domain`, `Infrastructure`, atau `Shared`.
- Pastikan perubahan baru tidak menaruh business rule di controller, blade, command, atau model aktif record consumer.
- Jika menemukan area lama yang belum DDD, prioritaskan memindahkan logic baru ke module, bukan memperbesar file legacy.

## Larangan Keras

- Jangan menambah query `Model::query()` baru di application service.
- Jangan menambah validasi manual dengan `Validator::make()` di controller bila bisa memakai Form Request.
- Jangan menambah route baru yang langsung memanggil controller legacy bila module baru belum dipakai.
- Jangan mencampur response HTML, JSON, dan domain rule dalam satu class.
- Jangan membuat helper global untuk menyiasati dependency injection.
- Jangan mengubah skema, API contract, atau naming penting tanpa memperbarui dokumentasi.

## Aturan Implementasi Detail

- Gunakan constructor injection untuk dependency.
- Tambah method baru di repository interface sebelum membuat implementasi infrastructure.
- Jika use case cukup besar, buat DTO atau payload object.
- Reuse `App\\Shared` untuk concern lintas module seperti response envelope, exception domain, utilitas arsitektural, atau trait yang memang generik.
- Nama class harus menggambarkan capability bisnis, misalnya `CreateOrderController`, `VerifyPaymentService`, `CustomerRepositoryInterface`.
- Jika ada write use case kompleks, bungkus orchestration di application service dan letakkan transaksi DB di boundary yang jelas.

## Checklist Sebelum Menyelesaikan Task

- Struktur file mengikuti module yang benar.
- Tidak ada query baca/tulis baru di controller.
- Tidak ada business rule baru di blade atau Livewire view.
- Error message penting tetap eksplisit.
- Test minimal yang relevan sudah dijalankan.
- Jika belum semua legacy sempat dimigrasikan, sebutkan sisa area itu secara eksplisit.

## Definition Of Done

- Arsitektur tetap konsisten dengan DDD.
- Tidak ada magic number tanpa alasan yang jelas.
- Naming, validation, dan error handling rapi serta konsisten.
- Test relevan dijalankan atau alasan kenapa belum bisa dijalankan dijelaskan.
- Dokumentasi ikut diperbarui jika kontrak API, struktur module, atau aturan kerja agent berubah.

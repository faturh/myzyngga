# Load Testing — myzyngga.vercel.app
# Menggunakan k6 v2.x

## Cara Menjalankan

Pastikan k6 sudah terinstall:
```bash
k6 version
```

---

## Skenario 1: Landing Page (50 user bersamaan)

```bash
k6 run load_tests/skenario1_landing_page.js
```

**Apa yang diuji:** Kemampuan server menyajikan halaman publik kepada banyak pengunjung bersamaan.

---

## Skenario 2: Cek Nota Publik (50 user bersamaan)

```bash
k6 run load_tests/skenario2_cek_nota.js
```

**Apa yang diuji:** Kemampuan fitur pelacakan nota (tanpa login) menangani request bersamaan.

> ⚠️ Catatan: Endpoint ini akan mengembalikan "tidak ditemukan" karena data test tidak ada — yang diuji adalah response time dan status HTTP, bukan konten data.

---

## Skenario 3: Proses Login (30 user bersamaan)

Sebelum menjalankan, **edit file** `load_tests/skenario3_login.js` dan ganti:
```javascript
email: 'testpelanggan@example.com', // ← Email akun test
password: 'password123',            // ← Password akun test
```

```bash
k6 run load_tests/skenario3_login.js
```

**Apa yang diuji:** Kemampuan sistem autentikasi menangani banyak percobaan login bersamaan.

---

## Cara Membaca Hasil k6

```
http_req_duration..............: avg=412ms  p(95)=1.8s  max=4.2s
```

| Metrik | Penjelasan | Target Lulus |
|--------|------------|--------------|
| `avg` | Rata-rata waktu respons | < 2 detik |
| `p(95)` | 95% request selesai dalam waktu ini | < 3 detik |
| `max` | Request paling lambat | < 5 detik |
| `errors` | Persentase request gagal | < 5% |

---

## Template Tabel Hasil untuk Skripsi

| Skenario | Jumlah User | Rata-rata Respons | p(95) | Error Rate | Keterangan |
|----------|-------------|-------------------|-------|------------|------------|
| Landing Page | 50 | ... ms | ... ms | ...% | Lulus/Tidak |
| Cek Nota | 50 | ... ms | ... ms | ...% | Lulus/Tidak |
| Login | 30 | ... ms | ... ms | ...% | Lulus/Tidak |

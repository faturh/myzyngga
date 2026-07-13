import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  // Konfigurasi tahapan uji beban (Stages)
  stages: [
    { duration: '10s', target:  50},  // Naik ke 3 pengguna virtual dalam 10 detik
    { duration: '20s', target: 50 },  // Tahan di 3 pengguna virtual selama 20 detik
    { duration: '10s', target: 0 },  // Turun ke 0 dalam 10 detik
  ],
  thresholds: {
    http_req_failed: ['rate<0.10'],     // Batas error maksimal 10%
    http_req_duration: ['p(95)<10000'],  // p(95) di bawah 10 detik karena server lokal single-thread
  },
};

const BASE_URL = 'http://localhost:8000'; // Ubah sesuai domain/URL target Anda jika sudah di-deploy

export default function () {
  // Setiap Virtual User (VU) di k6 memiliki session/cookies terisolasi sendiri otomatis.

  // 1. Uji Login Operator
  let loginRes = http.post(`${BASE_URL}/login`, {
    email: 'admin@zyngga.com',
    password: 'password',
  }, {
    headers: { 'Accept': 'application/json' },
    redirects: 0, // Mencegah redirect otomatis ke dashboard agar server lokal tidak terbebani
  });

  check(loginRes, {
    '1. Login berhasil (status 200 atau 302)': (r) => r.status === 200 || r.status === 302,
  });

  sleep(1); // Simulasi waktu jeda berfikir user

  // Apabila login berhasil (200 OK atau 302 Redirect), lanjutkan mengakses halaman-halaman dashboard operator
  if (loginRes.status === 200 || loginRes.status === 302) {
    
    // 2. Uji Penerimaan Pesanan (View Pesanan Masuk)
    let pesananRes = http.get(`${BASE_URL}/admin/riwayat-pesanan`, {
      headers: { 'Accept': 'application/json' },
    });
    check(pesananRes, {
      '2. View pesanan masuk sukses (status 200)': (r) => r.status === 200,
    });

    sleep(1);

    // 3. Uji Rekap Gaji (Kalkulasi Gaji Karyawan)
    let gajiRes = http.get(`${BASE_URL}/admin/gaji-karyawan`, {
      headers: { 'Accept': 'application/json' },
    });
    check(gajiRes, {
      '3. View rekap gaji sukses (status 200)': (r) => r.status === 200,
    });

    sleep(1);

    // 4. Uji Pencatatan Keuangan (Monitoring Saldo)
    let keuanganRes = http.get(`${BASE_URL}/admin/keuangan`, {
      headers: { 'Accept': 'application/json' },
    });
    check(keuanganRes, {
      '4. View monitoring saldo sukses (status 200)': (r) => r.status === 200,
    });

    sleep(2);
  }
}

import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate } from 'k6/metrics';

export let errorRate = new Rate('errors');

export let options = {
  stages: [
    { duration: '10s', target: 10 },
    { duration: '20s', target: 30 },  // Login lebih terbatas, jangan terlalu agresif
    { duration: '10s', target: 0 },
  ],
  thresholds: {
    http_req_duration: ['p(95)<5000'], // Login boleh lebih lambat, maks 5 detik
    errors: ['rate<0.10'],
  },
};

const BASE_URL = 'https://myzyngga.vercel.app';

export default function () {
  // Step 1: Ambil halaman login untuk mendapatkan CSRF token
  let loginPage = http.get(`${BASE_URL}/login`, {
    tags: { name: 'HalamanLogin' },
  });

  check(loginPage, {
    'halaman login terbuka': (r) => r.status === 200,
  });

  // Ambil CSRF token dari response HTML
  let csrfToken = loginPage.html().find('input[name="_token"]').attr('value');

  sleep(1);

  // Step 2: Submit form login
  // CATATAN: Ganti email dan password dengan akun testing yang valid
  let loginRes = http.post(
    `${BASE_URL}/login`,
    {
      _token: csrfToken,
      email: 'testpelanggan@example.com', // ← Ganti dengan email test kamu
      password: 'password123',            // ← Ganti dengan password test kamu
    },
    {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'text/html,application/xhtml+xml',
      },
      tags: { name: 'ProseLogin' },
      redirects: 5,
    }
  );

  check(loginRes, {
    'login berhasil atau diarahkan': (r) => r.status < 400,
    'response time < 5s': (r) => r.timings.duration < 5000,
  }) || errorRate.add(1);

  sleep(2);
}

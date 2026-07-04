import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate } from 'k6/metrics';

// Metrik custom: tingkat kegagalan
export let errorRate = new Rate('errors');

// Konfigurasi skenario
export let options = {
  stages: [
    { duration: '10s', target: 10 },  // Naik bertahap ke 10 user
    { duration: '20s', target: 50 },  // Tahan di 50 user selama 20 detik
    { duration: '10s', target: 0 },   // Turun ke 0
  ],
  thresholds: {
    http_req_duration: ['p(95)<3000'], // 95% request harus selesai < 3 detik
    errors: ['rate<0.05'],             // Tingkat error harus < 5%
  },
};

const BASE_URL = 'https://myzyngga.vercel.app';

export default function () {
  // Skenario 1: Akses landing page publik
  let res = http.get(`${BASE_URL}/`, {
    tags: { name: 'LandingPage' },
  });

  check(res, {
    'status 200': (r) => r.status === 200,
    'response time < 3s': (r) => r.timings.duration < 3000,
    'halaman dimuat': (r) => r.body && r.body.length > 0,
  }) || errorRate.add(1);

  sleep(1);
}

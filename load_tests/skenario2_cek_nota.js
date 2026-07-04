import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate } from 'k6/metrics';

export let errorRate = new Rate('errors');

export let options = {
  stages: [
    { duration: '10s', target: 10 },
    { duration: '20s', target: 50 },
    { duration: '10s', target: 0 },
  ],
  thresholds: {
    http_req_duration: ['p(95)<3000'],
    errors: ['rate<0.05'],
  },
};

const BASE_URL = 'https://myzyngga.vercel.app';

export default function () {
  // Skenario 2: Cek nota publik (tanpa login)
  // Endpoint ini mengirim query nota + 4 digit terakhir telepon
  let res = http.post(
    `${BASE_URL}/order/check`,
    {
      query: 'ZYN-001',
      phone_last_4: '1234',
    },
    {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'text/html,application/xhtml+xml',
      },
      tags: { name: 'CekNota' },
    }
  );

  check(res, {
    'status berhasil (200 atau 302)': (r) => r.status < 400,
    'response time < 3s': (r) => r.timings.duration < 3000,
  }) || errorRate.add(1);

  sleep(1);
}

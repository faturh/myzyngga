import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';

// Custom Metrics untuk Visualisasi Grafana
const successRate = new Rate('successful_requests');
const responseTimeTrend = new Trend('custom_response_time_ms');
const errorCount = new Counter('error_count');

// Konfigurasi Pengujian Beban (Load Test): 3 Virtual Users (VUs)
export const options = {
    scenarios: {
        operator_load_test: {
            executor: 'constant-vus',
            vus: 3,                  // 3 Virtual Users (3 Penguji)
            duration: '30s',         // Durasi pengujian 30 detik
        },
    },
    thresholds: {
        http_req_failed: ['rate<0.01'],    // Ambang batas error rate < 1%
        http_req_duration: ['p(95)<3000'], // 95% request diselesaikan di bawah 3000ms (3 detik)
        successful_requests: ['rate>0.99'],
    },
};

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';

// SETUP: Autentikasi Login awal sebelum pengujian dilakukan oleh 3 VUs
export function setup() {
    console.log(`[SETUP] Memulai Login Autentikasi ke ${BASE_URL}/login ...`);
    const loginPayload = {
        email: 'admin@zyngga.com',
        password: 'password',
    };

    const params = {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        },
        redirects: 5,
    };

    const res = http.post(`${BASE_URL}/login`, loginPayload, params);
    
    check(res, {
        'Login awal berhasil (Status 200/302)': (r) => r.status === 200 || r.status === 302,
    });

    const jar = http.cookieJar();
    const cookies = jar.cookiesForURL(BASE_URL);

    return { cookies: cookies };
}

export default function (data) {
    const jar = http.cookieJar();
    
    // Melampirkan cookie sesi login ke Virtual User
    if (data && data.cookies && data.cookies.zyngga_session) {
        jar.set(BASE_URL, 'zyngga_session', data.cookies.zyngga_session[0]);
    }

    const headers = {
        'Accept': 'application/json',
    };

    // Skenario 1: Monitoring Dashboard Operator
    group('1. Dashboard Operator', function () {
        const res = http.get(`${BASE_URL}/admin/dashboard`, { headers });
        const pass = check(res, {
            'Dashboard Status 200': (r) => r.status === 200,
        });

        successRate.add(pass);
        responseTimeTrend.add(res.timings.duration);
        if (!pass) errorCount.add(1);
    });

    sleep(1);

    // Skenario 2: Antrean Pesanan Aktif (Riwayat Pesanan)
    group('2. View Daftar Pesanan', function () {
        const res = http.get(`${BASE_URL}/admin/riwayat-pesanan`, { headers });
        const pass = check(res, {
            'Riwayat Pesanan Status 200': (r) => r.status === 200,
        });

        successRate.add(pass);
        responseTimeTrend.add(res.timings.duration);
        if (!pass) errorCount.add(1);
    });

    sleep(1);

    // Skenario 3: Realtime Counts Indicator
    group('3. Realtime Counts API', function () {
        const res = http.get(`${BASE_URL}/admin/riwayat-pesanan/counts`, { headers });
        const pass = check(res, {
            'Realtime Counts Status 200': (r) => r.status === 200,
        });

        successRate.add(pass);
        responseTimeTrend.add(res.timings.duration);
        if (!pass) errorCount.add(1);
    });

    sleep(1);
}

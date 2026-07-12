import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    vus: 50,
    duration: '30s',
    thresholds: {
        http_req_duration: ['p(95)<2000'],
        http_req_failed: ['rate<0.01'],
    },
};

const BASE_URL = 'http://127.0.0.1:8000/api/v1';

export function setup() {
    // Register a temporary user to get a valid token
    const uniqueId = Date.now();
    const payload = JSON.stringify({
        name: 'Load Test User',
        username: `loadtest_${uniqueId}`,
        email: `loadtest_${uniqueId}@example.com`,
        phone: '081234567890',
        password: 'password123',
        password_confirmation: 'password123'
    });

    const params = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
    };

    const res = http.post(`${BASE_URL}/auth/register`, payload, params);
    
    let token = '';
    if (res.status === 201) {
        token = res.json('access_token');
    } else {
        console.error('Setup failed: Could not register test user', res.body);
    }
    
    return { token: token };
}

export default function (data) {
    const params = {
        headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${data.token}`
        },
    };

    // 1. Fetch Payment Methods
    const resPayment = http.get(`${BASE_URL}/payment/methods`, params);
    check(resPayment, {
        'GET payment methods status is 200': (r) => r.status === 200,
    });

    // 2. Fetch Order History
    const resHistory = http.get(`${BASE_URL}/orders/history`, params);
    check(resHistory, {
        'GET order history status is 200': (r) => r.status === 200,
    });

    // 3. Fetch Notifications
    const resNotif = http.get(`${BASE_URL}/customer/notifications`, params);
    check(resNotif, {
        'GET notifications status is 200': (r) => r.status === 200,
    });

    sleep(1);
}


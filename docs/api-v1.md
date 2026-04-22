# API V1 Contract

Base path: `/api/v1`

## Response Envelope

Semua endpoint mengembalikan struktur:

```json
{
  "data": {},
  "meta": {},
  "errors": null
}
```

Jika error:

```json
{
  "data": null,
  "errors": {
    "message": "Deskripsi error",
    "context": {}
  }
}
```

## Order Module

- `POST /orders` create order
- `GET /orders/history` list riwayat order user login
- `GET /orders/{orderId}` detail order
- `PATCH /orders/{orderId}/status` update status order (admin)

## Customer Module

- `GET /customer/profile` profil pelanggan login
- `PUT /customer/address` upsert alamat default pelanggan
- `PUT /customer/preferences` upsert preferensi default pelanggan

## Payment Module

- `GET /payment/methods` daftar metode pembayaran
- `POST /payments/{orderId}/verify` verifikasi pembayaran (admin)

## Admin Module

- `GET /admin/dashboard` ringkasan metrik operasional
- `POST /admin/cabang` create cabang
- `POST /admin/jenis-layanan` create jenis layanan
- `POST /admin/transaksi/manual` create transaksi manual

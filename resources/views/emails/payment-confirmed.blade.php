<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Lunas – Zyngga Laundry</title>
    <style>
        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f4f6fa;
            color: #333333;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #1660c1;
            padding: 40px 20px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 10px 0 0 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .badge {
            display: inline-block;
            background-color: #e9f7ee;
            color: #10b981;
            padding: 6px 16px;
            border-radius: 99px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #f8fafc;
            border-radius: 12px;
        }
        .meta-table td {
            padding: 14px 16px;
            font-size: 14px;
            border-bottom: 1px solid #edf2f7;
        }
        .meta-table td:first-child {
            color: #718096;
            font-weight: 500;
        }
        .meta-table td:last-child {
            text-align: right;
            font-weight: 600;
            color: #2d3748;
        }
        .meta-table tr:last-child td {
            border-bottom: none;
        }
        .footer {
            background-color: #f7fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #a0aec0;
            border-top: 1px solid #edf2f7;
        }
        .btn {
            display: block;
            background-color: #1660c1;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 20px;
            border-radius: 99px;
            text-align: center;
            font-weight: 600;
            margin: 30px 0 10px 0;
        }
        .btn:hover {
            background-color: #124da0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://res.cloudinary.com/dv52j1qf0/image/upload/v1718970000/logo-laundry-simokerto.png" alt="Zyngga Laundry" style="width: 60px; border-radius: 99px;">
            <h1>Zyngga Laundry</h1>
        </div>
        <div class="content">
            <center>
                <div class="badge">Pembayaran Lunas</div>
            </center>
            <p>Halo <strong>{{ $transaksi->pelanggan->nama }}</strong>,</p>
            <p>Terima kasih telah melakukan pembayaran. Kami ingin mengonfirmasi bahwa pembayaran Anda untuk transaksi berikut telah kami terima secara penuh.</p>
            
            <table class="meta-table">
                <tr>
                    <td>Nomor Nota</td>
                    <td>{{ $transaksi->nota }}</td>
                </tr>
                <tr>
                    <td>Metode Pembayaran</td>
                    <td>{{ strtoupper($transaksi->jenis_pembayaran) }}</td>
                </tr>
                <tr>
                    <td>Total Bayar</td>
                    <td>Rp{{ number_format($transaksi->total_bayar_akhir, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Status Pembayaran</td>
                    <td>LUNAS</td>
                </tr>
            </table>

            <a href="{{ route('public.cetak-struk', $transaksi->id) }}" target="_blank" class="btn">
                Lihat & Cetak Nota Digital
            </a>

            <p style="font-size: 13px; color: #718096; text-align: center; margin-top: 20px;">
                Jika ada kendala mengenai transaksi ini, silakan hubungi kami melalui Pusat Bantuan di aplikasi Anda.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Zyngga Laundry. Sukabirus, Bandung Selatan. All rights reserved.
        </div>
    </div>
</body>
</html>

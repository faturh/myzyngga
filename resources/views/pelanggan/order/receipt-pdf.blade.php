<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Transaksi - {{ $order['nota_layanan'] }}</title>
    <style>
        @page {
            margin: 0px;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 30px 15px 15px 15px;
            line-height: 1.4;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header .title {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header .info {
            font-size: 10px;
            line-height: 1.2;
            color: #333;
        }
        .customer-name {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
            text-transform: uppercase;
        }
        .info-grid {
            width: 100%;
            font-size: 10px;
            margin-bottom: 10px;
        }
        .info-grid td {
            vertical-align: top;
            padding: 1px 0;
        }
        .dashed-line {
            border-bottom: 1px dashed #000;
            margin: 8px 0;
        }
        .items-list {
            width: 100%;
            font-size: 10px;
            margin: 10px 0;
        }
        .items-list td {
            padding: 2px 0;
            vertical-align: bottom;
        }
        .totals-section {
            width: 100%;
            font-size: 10px;
            margin-top: 5px;
        }
        .totals-section td {
            padding: 2px 0;
            vertical-align: top;
        }
        .keterangan {
            margin-top: 15px;
            font-size: 10px;
        }
        .keterangan table {
            width: 100%;
            font-size: 10px;
        }
        .keterangan td {
            padding: 1px 0;
        }
        .footer-notes {
            margin-top: 20px;
            font-size: 9px;
            line-height: 1.3;
        }
        .footer-notes ul {
            padding-left: 0;
            margin: 5px 0;
            list-style-type: none;
        }
        .footer-notes li {
            margin-bottom: 2px;
        }
        .footer-notes li:before {
            content: "- ";
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">ZYNGGA LAUNDRY</div>
        <div class="info">
            (Depan Roemah Kita)<br>
            0812 200 500 32<br>
            Sukabirus A1 no. 1 (Gor Sukabirus)<br>
            0821 2532 2500
        </div>
    </div>

    <div class="customer-name">
        {{ $order['customer_name'] }}
    </div>

    <table class="info-grid">
        <tr>
            <td width="35%">No Nota</td>
            <td width="65%" class="text-right">{{ $order['nota_layanan'] }}</td>
        </tr>
        <tr>
            <td>Tgl Masuk</td>
            <td class="text-right">{{ $order['order_date'] }}</td>
        </tr>
        <tr>
            <td>Est Selesai</td>
            <td class="text-right">{{ $order['estimated_finished'] }}</td>
        </tr>
    </table>

    <div class="dashed-line"></div>

    <table class="items-list">
        @foreach($order['items'] ?? [] as $item)
            @php
                $qty = $item['quantity'] ?? 1;
                $price = $item['price'] ?? 0;
                $total = $item['total'] ?? ($price * $qty);
            @endphp
            <tr>
                <td colspan="2">
                    {{ $item['name'] }}
                </td>
            </tr>
            <tr>
                <td width="65%">{{ $qty }} Pcs x {{ number_format($price, 0, '', '') }}</td>
                <td width="35%" class="text-right">{{ number_format($total, 0, '', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="dashed-line"></div>

    <table class="totals-section">
        <tr>
            <td width="30%">
                {{ strtoupper($order['payment_status'] === 'Lunas' || $order['payment_status'] === 'paid' ? 'LUNAS' : 'BELUM LUNAS') }}
            </td>
            <td width="70%">
                <table style="width: 100%;">
                    <tr>
                        <td width="55%">Subtotal</td>
                        <td width="45%" class="text-right">(+) {{ number_format($order['subtotal'], 0, '', '.') }}</td>
                    </tr>
                    @if(isset($order['upgrade_fee']) && $order['upgrade_fee'] > 0)
                    <tr>
                        <td>Upgrade</td>
                        <td class="text-right">{{ number_format($order['upgrade_fee'], 0, '', '.') }}</td>
                    </tr>
                    @endif
                    @if(isset($order['tax']) && $order['tax'] > 0)
                    <tr>
                        <td>Pajak</td>
                        <td class="text-right">{{ number_format($order['tax'], 0, '', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Diskon</td>
                        <td class="text-right">0</td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td class="text-right">{{ number_format($order['total'], 0, '', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="dashed-line"></div>

    <div class="keterangan">
        <div>Keterangan</div>
        <table style="margin-top: 5px;">
            <tr>
                <td width="30%">Parfum</td>
                <td width="70%">TANPA PEWANGI</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>Sistem Zyngga</td>
            </tr>
        </table>
    </div>

    <div class="footer-notes">
        <ul>
            <li>Harap membawa bon saat pengambilan</li>
            <li>Ganti maksimal: Satuan x5, Kiloan x2</li>
            <li>Batas komplain 1x24 jam</li>
            <li>Gratis pickup dan delivery</li>
            <li>Diluar tanggung jawab kami cucian lebih 6 bulan</li>
        </ul>
    </div>

</body>
</html>

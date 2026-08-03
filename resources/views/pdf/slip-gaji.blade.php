<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $detail->nama }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
            padding: 25px 30px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header .company-name {
            font-size: 13px;
            font-weight: bold;
            color: #1a56db;
            letter-spacing: 1px;
        }
        .company-logo {
            height: 32px;
            width: auto;
            margin-bottom: 4px;
        }
        .header .electronic-note {
            font-size: 9px;
            font-style: italic;
            color: #888;
            margin-top: 2px;
        }
        .header h1 {
            font-size: 13px;
            margin: 2px 0;
            color: #1a1a1a;
        }
        .identity-section {
            margin-bottom: 18px;
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
        }
        .identity-table {
            width: 100%;
            border-collapse: collapse;
        }
        .identity-table td {
            vertical-align: top;
            padding: 2px 6px;
            font-size: 11px;
        }
        .left-col {
            width: 50%;
            line-height: 1.6;
            color: #333;
            font-style: italic;
        }
        .right-col {
            width: 50%;
        }
        .employee-info td {
            padding: 0px 4px;
            line-height: 1.6;
        }
        .identity-table .label {
            font-weight: bold;
            width: 85px;
            color: #555;
        }
        .section-title {
            font-weight: bold;
            font-size: 12px;
            padding: 6px 8px;
            margin-top: 14px;
            margin-bottom: 2px;
        }
        .section-title.penerimaan {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .section-title.pengurangan {
            background: #ffebee;
            color: #c62828;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }
        .detail-table th {
            background: #f5f5f5;
            padding: 5px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
        }
        .detail-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 11px;
        }
        .detail-table .text-right {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            font-size: 12px;
            border-top: 2px solid #333;
            padding-top: 6px;
        }
        .grand-total td {
            font-weight: bold;
            font-size: 13px;
            border-top: 3px double #1a56db;
            padding-top: 6px;
            color: #1a56db;
        }
        .terbilang {
            text-align: center;
            font-size: 10px;
            font-style: italic;
            padding: 8px;
            margin-top: 4px;
            border: 1px dashed #999;
            background: #fafafa;
        }
        .spacer {
            height: 20px;
        }
        .footer-line {
            border: none;
            border-top: 1px solid #ccc;
            margin: 20px 0 10px 0;
        }
        .signature-section {
            text-align: center;
            margin-top: 10px;
        }
        .signature-section .city-date {
            font-size: 11px;
            margin-bottom: 40px;
        }
        .signature-section .company-sign {
            font-weight: bold;
            font-size: 12px;
            margin-top: 5px;
        }
        .watermark {
            position: fixed;
            top: 42%;
            left: 8%;
            font-size: 52px;
            font-weight: bold;
            color: #cccccc;
            opacity: 0.2;
            text-transform: uppercase;
            letter-spacing: 12px;
            z-index: 9999;
            pointer-events: none;
            transform: rotate(-40deg);
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="watermark">Private &amp; Confidential</div>
    <div class="header">
        @if($logo)
            <img src="{{ $logo }}" alt="Logo" class="company-logo">
        @endif
        <div class="company-name">PT. JOHEN SUKSES ABADI</div>
        <h1>SLIP GAJI</h1>
        <div class="electronic-note">Slip ini dicetak secara elektronik</div>
    </div>

    <div class="identity-section">
        <table class="identity-table">
            <tr>
                <td class="left-col">
                    <strong>PT. Johen Sukses Abadi</strong><br>
                    Summarecon Gedebage<br>
                    Ruko Plaza Topaz Commercial No.60, Summarecon<br>
                    Gedebage, Kota Bandung, Jawa Barat, 40294
                </td>
                <td class="right-col">
                    <table class="employee-info">
                        <tr><td class="label">Periode</td><td>: {{ $periode }}</td></tr>
                        <tr><td class="label">NIK</td><td>: {{ $detail->nik }}</td></tr>
                        <tr><td class="label">Karyawan</td><td>: {{ $detail->nama }}</td></tr>
                        <tr><td class="label">Jabatan</td><td>: {{ $detail->jabatan }}</td></tr>
                        <tr><td class="label">Divisi</td><td>: {{ $detail->divisi }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title penerimaan">PENERIMAAN</div>
    <table class="detail-table">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td class="text-right">{{ number_format($detail->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tambahan Upah</td>
                <td class="text-right">{{ $detail->tambahan_upah > 0 ? number_format($detail->tambahan_upah, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr>
                <td>Bonus</td>
                <td class="text-right">{{ $detail->bonus > 0 ? number_format($detail->bonus, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr>
                <td>THR</td>
                <td class="text-right">{{ $detail->thr > 0 ? number_format($detail->thr, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr>
                <td>Apresiasi</td>
                <td class="text-right">{{ $detail->apresiasi > 0 ? number_format($detail->apresiasi, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr>
                <td>Tunjangan Jabatan</td>
                <td class="text-right">{{ $detail->tunjangan_jabatan > 0 ? number_format($detail->tunjangan_jabatan, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr>
                <td>Premi BPJS Kesehatan (4%)</td>
                <td class="text-right">{{ $detail->premi_bpjs_kesehatan > 0 ? number_format($detail->premi_bpjs_kesehatan, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL PENGHASILAN BRUTO</td>
                <td class="text-right">Rp {{ number_format($detail->total_penghasilan_bruto, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title pengurangan">PENGURANGAN</div>
    <table class="detail-table">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>THR yang sudah dibayarkan</td>
                <td class="text-right">{{ $detail->thr_dibayarkan > 0 ? number_format($detail->thr_dibayarkan, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr>
                <td>Potongan pinjaman karyawan</td>
                <td class="text-right">{{ $detail->potongan_pinjaman > 0 ? number_format($detail->potongan_pinjaman, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr>
                <td>Potongan Absensi / Jam Kerja</td>
                <td class="text-right">{{ $detail->potongan_absensi > 0 ? number_format($detail->potongan_absensi, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr>
                <td>Potongan BPJS Kesehatan (4%)</td>
                <td class="text-right">{{ $detail->potongan_bpjs_kesehatan_4 > 0 ? number_format($detail->potongan_bpjs_kesehatan_4, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr>
                <td>Potongan BPJS Kesehatan (1%)</td>
                <td class="text-right">{{ $detail->potongan_bpjs_kesehatan_1 > 0 ? number_format($detail->potongan_bpjs_kesehatan_1, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL PENGELUARAN</td>
                <td class="text-right">Rp {{ number_format($detail->total_pengeluaran, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="detail-table" style="margin-top: 6px;">
        <tr class="grand-total">
            <td>TOTAL DITERIMA KARYAWAN</td>
            <td class="text-right">Rp {{ number_format($detail->take_home_pay, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="terbilang">
        # {{ terbilang($detail->take_home_pay) }} Rupiah #
    </div>

    <hr class="footer-line">

    <div class="signature-section">
        <div class="city-date">Bandung, 30 {{ $periode }}</div>

        <div style="margin-top: 50px;"></div>

        <div class="company-sign">PT. Johen Sukses Abadi</div>
    </div>
</body>
</html>

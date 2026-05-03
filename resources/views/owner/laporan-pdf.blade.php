<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan {{ $bulan }} {{ $tahun }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3F4F44;
        }

        .header h1 {
            color: #3F4F44;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header h2 {
            color: #666;
            font-size: 18px;
            font-weight: normal;
            margin-bottom: 3px;
        }

        .header p {
            color: #999;
            font-size: 10px;
        }

        /* Summary Stats */
        .summary-stats {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
        }

        .stat-box h3 {
            font-size: 20px;
            color: #3F4F44;
            margin-bottom: 5px;
        }

        .stat-box p {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }

        /* Section Title */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #3F4F44;
            margin-top: 25px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e0e0e0;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background-color: #3F4F44;
            color: white;
        }

        table thead th {
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
        }

        table tbody td {
            padding: 7px 6px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 10px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #f0f0f0;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        /* Status Badge */
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Total Section */
        .total-section {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .total-label {
            display: table-cell;
            width: 70%;
            text-align: right;
            padding-right: 20px;
            font-weight: bold;
        }

        .total-value {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-size: 13px;
        }

        .grand-total {
            border-top: 2px solid #3F4F44;
            padding-top: 8px;
            margin-top: 8px;
        }

        .grand-total .total-value {
            font-size: 16px;
            font-weight: bold;
            color: #3F4F44;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 9px;
            color: #999;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* No Data */
        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>AYUMART</h1>
            <h2>LAPORAN PENJUALAN</h2>
            <p>Periode: {{ $bulan }} {{ $tahun }}</p>
            <p>Dicetak pada: {{ $tanggalCetak }}</p>
        </div>

        <!-- Summary Statistics -->
        <div class="summary-stats">
            <div class="stat-box">
                <h3>{{ $totalTransaksi }}</h3>
                <p>Total Transaksi</p>
            </div>
            <div class="stat-box">
                <h3>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
                <p>Total Penjualan</p>
            </div>
            <div class="stat-box">
                <h3>Rp {{ number_format($totalDiskon, 0, ',', '.') }}</h3>
                <p>Total Diskon</p>
            </div>
            <div class="stat-box">
                <h3>Rp {{ number_format($pendapatanBersih, 0, ',', '.') }}</h3>
                <p>Pendapatan Bersih</p>
            </div>
        </div>

        <!-- Top Products Section -->
        <div class="section-title">10 PRODUK TERLARIS</div>
        @if($topProducts->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 55%;">Nama Produk</th>
                    <th style="width: 20%;" class="text-right">Jumlah Terjual</th>
                    <th style="width: 20%;" class="text-right">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $index => $product)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $product->nama_produk }}</td>
                    <td class="text-right">{{ number_format($product->total_terjual, 0, ',', '.') }} pcs</td>
                    <td class="text-right">Rp {{ number_format($product->total_pendapatan, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">Tidak ada data produk untuk periode ini</div>
        @endif

        <!-- Transactions List -->
        <div class="section-title">DAFTAR TRANSAKSI</div>
        @if($transactions->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Kode Transaksi</th>
                    <th style="width: 10%;">Tanggal</th>
                    {{-- <th style="width: 18%;">Pelanggan</th> --}}
                    <th style="width: 15%;" class="text-right">Subtotal</th>
                    <th style="width: 12%;" class="text-right">Diskon</th>
                    <th style="width: 12%;" class="text-right">Ongkir</th>
                    <th style="width: 16%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $index => $transaction)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $transaction->kode_transaksi ?? 'N/A' }}</td>
                    <td>{{ $transaction->tanggal_transaksi ? \Carbon\Carbon::parse($transaction->tanggal_transaksi)->format('d/m/Y') : 'N/A' }}</td>
                    {{-- <td>{{ $transaction->pelanggan->name ?? 'N/A' }}</td> --}}
                    <td class="text-right">Rp {{ number_format($transaction->total_harga - ($transaction->ongkir ?? 0) + ($transaction->total_diskon ?? 0), 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($transaction->total_diskon ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($transaction->ongkir ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right text-bold">Rp {{ number_format($transaction->total_harga ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Summary -->
        <div class="total-section">
            <div class="total-row">
                <div class="total-label">Total Transaksi:</div>
                <div class="total-value">{{ $totalTransaksi }} transaksi</div>
            </div>
            <div class="total-row">
                <div class="total-label">Total Diskon:</div>
                <div class="total-value">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</div>
            </div>
            <div class="total-row">
                <div class="total-label">Total Ongkir:</div>
                <div class="total-value">Rp {{ number_format($totalOngkir, 0, ',', '.') }}</div>
            </div>
            <div class="total-row grand-total">
                <div class="total-label">TOTAL PENJUALAN:</div>
                <div class="total-value">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
            </div>
            <div class="total-row">
                <div class="total-label">PENDAPATAN BERSIH (Setelah Diskon):</div>
                <div class="total-value">Rp {{ number_format($pendapatanBersih, 0, ',', '.') }}</div>
            </div>
        </div>
        @else
        <div class="no-data">Tidak ada transaksi untuk periode ini</div>
        @endif

        <!-- Footer -->
        <div class="footer">
            {{-- <p>Laporan ini digenerate secara otomatis oleh sistem AyuMart</p> --}}
            <p>&copy; {{ date('Y') }} AyuMart - Supermarket Online</p>
        </div>
    </div>
</body>
</html>

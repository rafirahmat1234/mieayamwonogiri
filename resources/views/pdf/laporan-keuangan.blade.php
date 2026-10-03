<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 18px; }
        .header p  { margin: 4px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2d6a4f; color: white; padding: 8px; text-align: left; }
        td { padding: 7px 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background: #f4f4f4; }
        .badge-masuk  { background: #d4edda; color: #155724; padding: 2px 8px; border-radius: 10px; font-size: 11px; }
        .badge-keluar { background: #f8d7da; color: #721c24; padding: 2px 8px; border-radius: 10px; font-size: 11px; }
        .summary { margin-top: 20px; float: right; width: 280px; }
        .summary table td { font-size: 12px; }
        .summary .total { font-weight: bold; background: #2d6a4f; color: white; }
        .footer { margin-top: 60px; text-align: right; font-size: 11px; color: #888; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KEUANGAN UMKM</h2>
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Jenis</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $i => $t)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $t->keterangan }}</td>
                <td>
                    @if($t->jenis == 'pemasukan')
                        <span class="badge-masuk">Pemasukan</span>
                    @else
                        <span class="badge-keluar">Pengeluaran</span>
                    @endif
                </td>
                <td>Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td>Total Pemasukan</td>
                <td>Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td>Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td>Saldo Akhir</td>
                <td>Rp {{ number_format($saldo, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">Laporan ini digenerate otomatis oleh sistem.</div>
</body>
</html>
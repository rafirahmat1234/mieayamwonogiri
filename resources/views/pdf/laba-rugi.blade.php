<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 18px; }
        .header p  { margin: 4px 0; color: #666; }
        h3 { background: #2d6a4f; color: white; padding: 6px 10px; margin-top: 25px; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #eee; padding: 7px 8px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; }
        .subtotal td { font-weight: bold; background: #f0f0f0; }
        .laba   td { font-weight: bold; background: #d4edda; color: #155724; font-size: 13px; }
        .rugi   td { font-weight: bold; background: #f8d7da; color: #721c24; font-size: 13px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN LABA RUGI UMKM</h2>
        <p>Periode: {{ $namaBulan }} {{ $tahun }} &nbsp;|&nbsp; Dicetak: {{ now()->format('d-m-Y H:i') }}</p>
    </div>

    {{-- PENDAPATAN --}}
<h3>A. Pendapatan (Pemasukan)</h3>
<table>
    <thead>
        <tr><th>No</th><th>Tanggal</th><th>Keterangan</th><th>Jumlah</th></tr>
    </thead>
    <tbody>
        @forelse($pemasukan as $i => $p)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
            <td>{{ $p->keterangan ?? $p->catatan ?? '-' }}</td>
            <td>Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center">Tidak ada data</td></tr>
        @endforelse
        <tr class="subtotal">
            <td colspan="3">Total Pendapatan</td>
            <td>Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

{{-- BEBAN / PENGELUARAN --}}
<h3>B. Beban (Pengeluaran)</h3>
<table>
    <thead>
        <tr><th>No</th><th>Tanggal</th><th>Keterangan</th><th>Jumlah</th></tr>
    </thead>
    <tbody>
        @forelse($pengeluaran as $i => $p)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
            <td>{{ $p->keterangan ?? $p->catatan ?? '-' }}</td>
            <td>Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center">Tidak ada data</td></tr>
        @endforelse
        <tr class="subtotal">
            <td colspan="3">Total Beban</td>
            <td>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

{{-- HASIL LABA/RUGI --}}
<br>
<table>
    <tr class="{{ $labaRugi >= 0 ? 'laba' : 'rugi' }}">
    <td>{{ $labaRugi >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</td>
    <td style="text-align:right">Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}</td>
</tr>
</table>
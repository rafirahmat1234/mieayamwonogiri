<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; }
        h2   { text-align: center; }
        .tanggal { text-align: center; color: gray; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th    { background-color: #4CAF50; color: white; padding: 8px; text-align: left; }
        td    { padding: 7px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h2>Laporan Keuangan</h2>
    <p class="tanggal">Dicetak pada: {{ $tanggal }}</p>

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
                <td>{{ $t->tanggal }}</td>          {{-- sesuaikan kolom --}}
                <td>{{ $t->keterangan }}</td>       {{-- sesuaikan kolom --}}
                <td>{{ $t->jenis }}</td>            {{-- sesuaikan kolom --}}
                <td>Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
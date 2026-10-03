<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk - {{ $pesanan->nomor_pesanan }}</title>
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .struk-box { max-width: 380px; margin: 30px auto; border: 1px solid #ddd; padding: 20px; font-family: monospace; }
        .struk-box hr { border-top: 1px dashed #888; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="struk-box">
        <div class="text-center mb-2">
            <strong>MIE AYAM WONOGIRI</strong><br>
            <small>{{ \Carbon\Carbon::parse($pesanan->tanggal)->format('d M Y') }} - {{ $pesanan->nomor_pesanan }}</small>
        </div>
        <hr>
        @foreach($pesanan->detail as $item)
        <div class="d-flex justify-content-between small">
            <span>{{ $item->menu->nama_menu ?? '-' }} x{{ $item->jumlah }}</span>
            <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @endforeach
        <hr>
        <div class="d-flex justify-content-between font-weight-bold">
            <span>TOTAL</span>
            <span>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
        </div>
        <hr>
        <div class="text-center small">Terima kasih!</div>

        <div class="text-center mt-3 no-print">
            <button onclick="window.print()" class="btn btn-primary btn-sm">Cetak</button>
            <a href="{{ route('pos.index') }}" class="btn btn-secondary btn-sm">Pesanan Baru</a>
        </div>
    </div>
</body>
</html>

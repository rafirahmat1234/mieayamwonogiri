<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard - Mie Ayam Wonogiri</title>
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .badge-masuk  { background:#1cc88a; color:#fff; padding:4px 10px; border-radius:20px; font-size:11px; }
        .badge-keluar { background:#e74a3b; color:#fff; padding:4px 10px; border-radius:20px; font-size:11px; }
        .amount-in    { color:#1cc88a; font-weight:700; }
        .amount-out   { color:#e74a3b; font-weight:700; }
        .empty-state  { text-align:center; padding:40px 20px; color:#b7b9cc; }
        .empty-state i { font-size:3rem; margin-bottom:10px; display:block; }
        .section-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#858796; margin-bottom:10px; }
        .transaksi-scroll { max-height: 350px; overflow-y: auto; }
        .keterangan-cell { max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    </style>
</head>
<body id="page-top">
<div id="wrapper">

    <!-- SIDEBAR -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
    <img src="{{ asset('img/logo_mie_ayam.png') }}" alt="Logo" style="width:45px;height:45px;object-fit:contain;border-radius:50%;background:white;padding:3px;margin-right:8px">
    <div class="sidebar-brand-text" style="font-size:12px;line-height:1.3">Mie Ayam<br>Wonogiri</div>
</a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item active">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span>
            </a>
        </li>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Keuangan</div>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('pemasukan.index') }}">
                <i class="fas fa-fw fa-arrow-up" style="color:#1cc88a"></i><span>Pemasukan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('pengeluaran.index') }}">
                <i class="fas fa-fw fa-arrow-down" style="color:#e74a3b"></i><span>Pengeluaran</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('laporan.index') }}">
                <i class="fas fa-fw fa-chart-bar"></i><span>Laporan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('stok-bahan.index') }}">
                <i class="fas fa-fw fa-boxes"></i><span>Stok Bahan</span>
            </a>
        </li>
        <hr class="sidebar-divider">
<div class="sidebar-heading">Export PDF</div>
<li class="nav-item px-3 pb-2">
    <form method="GET" action="{{ route('export.laba-rugi') }}" target="_blank">
        <div class="mb-2">
            <label class="text-white small mb-1">Bulan</label>
            <select name="bulan" class="form-control form-control-sm">
                @foreach(range(1, 12) as $b)
                    <option value="{{ $b }}" {{ $b == now()->month ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate(null, $b, 1)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-2">
            <label class="text-white small mb-1">Tahun</label>
            <select name="tahun" class="form-control form-control-sm">
                @foreach(range(now()->year - 3, now()->year) as $t)
                    <option value="{{ $t }}" {{ $t == now()->year ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-warning btn-sm btn-block">
            <i class="fas fa-file-pdf"></i> Export Laba Rugi
        </button>
    </form>
</li>

        <hr class="sidebar-divider d-none d-md-block">
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- TOPBAR -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <span class="navbar-text font-weight-bold text-gray-800 ml-2">
                    📅 {{ now()->translatedFormat('l, d F Y') }}
                </span>
                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small font-weight-bold">
                                {{ Auth::user()->name }}
                            </span>
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:14px">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="container-fluid">

                <!-- Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-3">
                    <h1 class="h3 mb-0 text-gray-800">🍜 Dashboard Keuangan</h1>
                    <div>
                        <a href="{{ route('pemasukan.index') }}" class="btn btn-sm btn-success shadow-sm mr-1">
                            <i class="fas fa-arrow-up fa-sm"></i> Pemasukan
                        </a>
                        <a href="{{ route('pengeluaran.index') }}" class="btn btn-sm btn-danger shadow-sm">
                            <i class="fas fa-arrow-down fa-sm"></i> Pengeluaran
                        </a>
                    </div>
                </div>

                <!-- Stok Warning -->
                @if($stokWarning->count() > 0)
                <div class="alert alert-danger shadow-sm mb-3">
                    <strong><i class="fas fa-exclamation-triangle mr-2"></i>Stok Hampir Habis!</strong>
                    {{ $stokWarning->pluck('nama_bahan')->join(', ') }}
                    — <a href="{{ route('stok-bahan.index') }}" class="alert-link">Kelola Stok →</a>
                </div>
                @endif

                <!-- TOTAL KESELURUHAN -->
                <div class="section-label">📊 Total Keseluruhan</div>
                <div class="row mb-2">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pemasukan</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalMasukAll, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-arrow-up fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalKeluarAll, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-arrow-down fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total {{ $labaAll >= 0 ? 'Laba' : 'Rugi' }}</div>
                                        <div class="h5 mb-0 font-weight-bold {{ $labaAll >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format(abs($labaAll), 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-wallet fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Transaksi</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Transaksi::count() }} Transaksi</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-receipt fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BULAN INI -->
                <div class="section-label mt-2">📆 Bulan Ini — {{ now()->format('F Y') }}</div>
                <div class="row mb-4">
                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="card shadow h-100 py-2" style="border-left:4px solid #17a673">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1" style="color:#17a673">Pemasukan Bulan Ini</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalMasukBulan, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-calendar-check fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pengeluaran Bulan Ini</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalKeluarBulan, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-calendar-times fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ $labaBulan >= 0 ? 'Laba' : 'Rugi' }} Bulan Ini</div>
                                        <div class="h5 mb-0 font-weight-bold {{ $labaBulan >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format(abs($labaBulan), 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto"><i class="fas fa-chart-line fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GRAFIK & PIE -->
                <div class="row">
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">📈 Arus Kas 7 Hari Terakhir</h6>
                                <div class="small">
                                    <span class="mr-3"><i class="fas fa-circle text-success"></i> Pemasukan</span>
                                    <span><i class="fas fa-circle text-danger"></i> Pengeluaran</span>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($grafik->count() > 0)
                                <div class="chart-area"><canvas id="arusKasChart"></canvas></div>
                                @else
                                <div class="empty-state">
                                    <i class="fas fa-chart-line"></i>
                                    Belum ada transaksi 7 hari terakhir<br>
                                    <a href="{{ route('pemasukan.index') }}" class="btn btn-success btn-sm mt-2 mr-1">+ Pemasukan</a>
                                    <a href="{{ route('pengeluaran.index') }}" class="btn btn-danger btn-sm mt-2">+ Pengeluaran</a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">🥧 Pemasukan vs Pengeluaran</h6>
                            </div>
                            <div class="card-body">
                                @if($totalMasukAll > 0 || $totalKeluarAll > 0)
                                <div class="chart-pie pt-2 pb-2"><canvas id="pieChart"></canvas></div>
                                @else
                                <div class="empty-state"><i class="fas fa-chart-pie"></i>Belum ada data</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TRANSAKSI TERBARU & STOK -->
                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">🕐 Transaksi Terbaru</h6>
                                <div>
                                    <a href="{{ route('pemasukan.index') }}" class="btn btn-sm btn-success mr-1">Pemasukan</a>
                                    <a href="{{ route('pengeluaran.index') }}" class="btn btn-sm btn-danger">Pengeluaran</a>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                @if($terbaru->count() > 0)
                                <div class="table-responsive transaksi-scroll">
                                    <table class="table table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Keterangan</th>
                                                <th>Tipe</th>
                                                <th class="text-right">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($terbaru as $t)
                                            <tr>
                                                <td class="small text-muted">{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</td>
                                                <td class="keterangan-cell" title="{{ $t->keterangan }}">{{ $t->keterangan }}</td>
                                                <td>
                                                    @if($t->tipe == 'pemasukan')
                                                        <span class="badge-masuk">↑ Masuk</span>
                                                    @else
                                                        <span class="badge-keluar">↓ Keluar</span>
                                                    @endif
                                                </td>
                                                <td class="text-right {{ $t->tipe == 'pemasukan' ? 'amount-in' : 'amount-out' }}">
                                                    Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="empty-state">
                                    <i class="fas fa-receipt"></i>
                                    Belum ada transaksi<br>
                                    <a href="{{ route('pemasukan.index') }}" class="btn btn-success btn-sm mt-2 mr-1">+ Pemasukan</a>
                                    <a href="{{ route('pengeluaran.index') }}" class="btn btn-danger btn-sm mt-2">+ Pengeluaran</a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">📦 Stok Bahan Baku</h6>
                                <a href="{{ route('stok-bahan.index') }}" class="btn btn-sm btn-outline-primary">Kelola</a>
                            </div>
                            <div class="card-body">
                                @php $stokAll = \App\Models\StokBahan::orderBy('nama_bahan')->get(); @endphp
                                @if($stokAll->count() > 0)
                                    @foreach($stokAll as $s)
                                    @php
                                        $pct = $s->stok_minimum > 0 ? min(100, ($s->stok_saat_ini / ($s->stok_minimum * 3)) * 100) : 50;
                                        $bar = $s->stok_saat_ini <= $s->stok_minimum ? 'bg-danger' : ($pct < 60 ? 'bg-warning' : 'bg-success');
                                        $txt = $s->stok_saat_ini <= $s->stok_minimum ? 'text-danger' : ($pct < 60 ? 'text-warning' : 'text-success');
                                    @endphp
                                    <h4 class="small font-weight-bold">
                                        {{ $s->nama_bahan }}
                                        <span class="float-right {{ $txt }}">
                                            {{ $s->stok_saat_ini }} {{ $s->satuan }}
                                            @if($s->stok_saat_ini <= $s->stok_minimum) ⚠️ @endif
                                        </span>
                                    </h4>
                                    <div class="progress mb-3" style="height:8px">
                                        <div class="progress-bar {{ $bar }}" style="width:{{ $pct }}%"></div>
                                    </div>
                                    @endforeach
                                @else
                                <div class="empty-state">
                                    <i class="fas fa-box-open"></i>
                                    Belum ada stok<br>
                                    <a href="{{ route('stok-bahan.index') }}" class="btn btn-primary btn-sm mt-2">+ Tambah Bahan</a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>🍜 Mie Ayam Wonogiri &copy; {{ date('Y') }}</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

@if($grafik->count() > 0)
<script>
var days = [], masukData = [], keluarData = [];
for (var i = 6; i >= 0; i--) {
    var d = new Date(); d.setDate(d.getDate() - i);
    var dateStr = d.toISOString().slice(0, 10);
    var label = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    days.push(label);
    var found = @json($grafik).find(function(g) { return g.tanggal === dateStr; });
    masukData.push(found ? parseFloat(found.masuk) : 0);
    keluarData.push(found ? parseFloat(found.keluar) : 0);
}
new Chart(document.getElementById('arusKasChart'), {
    type: 'line',
    data: {
        labels: days,
        datasets: [{
            label: 'Pemasukan', data: masukData,
            borderColor: '#1cc88a', backgroundColor: 'rgba(28,200,138,0.1)',
            borderWidth: 2, fill: true, tension: 0.4
        },{
            label: 'Pengeluaran', data: keluarData,
            borderColor: '#e74a3b', backgroundColor: 'rgba(231,74,59,0.05)',
            borderWidth: 2, fill: true, tension: 0.4
        }]
    },
    options: {
        maintainAspectRatio: false, legend: { display: false },
        scales: { yAxes: [{ ticks: { callback: function(v) { return 'Rp '+(v/1000)+'k'; }}}]}
    }
});
</script>
@endif

@if($totalMasukAll > 0 || $totalKeluarAll > 0)
<script>
new Chart(document.getElementById('pieChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pemasukan', 'Pengeluaran'],
        datasets: [{ data: [{{ $totalMasukAll }}, {{ $totalKeluarAll }}], backgroundColor: ['#1cc88a', '#e74a3b'] }]
    },
    options: { maintainAspectRatio: false, legend: { display: true, position: 'bottom' }, cutoutPercentage: 70 }
});
</script>
@endif
</body>
</html>
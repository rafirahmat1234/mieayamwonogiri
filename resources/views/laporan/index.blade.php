<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Laporan - Mie Ayam Wonogiri</title>
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
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
        <li class="nav-item">
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
        <li class="nav-item active">
            <a class="nav-link" href="{{ route('laporan.index') }}">
                <i class="fas fa-fw fa-chart-bar"></i><span>Laporan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('stok-bahan.index') }}">
                <i class="fas fa-fw fa-boxes"></i><span>Stok Bahan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('pos.index') }}">
                <i class="fas fa-fw fa-cash-register"></i><span>Kasir (POS)</span>
            </a>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- TOPBAR -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
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

            <!-- MAIN CONTENT -->
            <div class="container-fluid">

                <!-- Header -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">📈 Laporan Keuangan</h1>
                    <form method="GET" action="{{ route('laporan.index') }}" class="form-inline">
                        <label class="mr-2 font-weight-bold small">Pilih Bulan:</label>
                        <input type="month" name="bulan" class="form-control form-control-sm mr-2"
                            value="{{ $bulan }}">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search mr-1"></i> Tampilkan
                        </button>
                    </form>
                </div>

                <!-- Ringkasan Laba Bersih -->
                <div class="card shadow mb-4" style="border-left: 4px solid {{ $labaBersih >= 0 ? '#f6c23e' : '#e74a3b' }}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-uppercase mb-1" style="color:{{ $labaBersih >= 0 ? '#f6c23e' : '#e74a3b' }}">
                                    {{ $labaBersih >= 0 ? '✅ Total Laba Bersih' : '❌ Total Kerugian' }}
                                </div>
                                <div class="h3 mb-0 font-weight-bold" style="color:{{ $labaBersih >= 0 ? '#f6c23e' : '#e74a3b' }}">
                                    Rp {{ number_format(abs($labaBersih), 0, ',', '.') }}
                                </div>
                                <div class="small text-muted mt-1">
                                    Pendapatan dikurangi biaya operasional (tidak termasuk belanja stok bahan)
                                </div>
                            </div>
                            <div class="col-auto text-right">
                                <div class="small text-muted mb-1">Pemasukan</div>
                                <div class="font-weight-bold text-success">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
                                <div class="small text-muted mt-2 mb-1">Biaya Operasional</div>
                                <div class="font-weight-bold text-danger">Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}</div>
                                <div class="small text-muted mt-2 mb-1">Belanja Stok Bahan</div>
                                <div class="font-weight-bold" style="color:#f6c23e">Rp {{ number_format($totalBelanjaStok, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Arus Kas -->
                <div class="card shadow mb-4" style="border-left: 4px solid #36b9cc">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-uppercase mb-1" style="color:#36b9cc">
                                    💰 Arus Kas Bersih Bulan Ini
                                </div>
                                <div class="h3 mb-0 font-weight-bold" style="color:{{ $arusKasBersih >= 0 ? '#36b9cc' : '#e74a3b' }}">
                                    Rp {{ number_format($arusKasBersih, 0, ',', '.') }}
                                </div>
                                <div class="small text-muted mt-1">
                                    Semua uang masuk dikurangi semua uang keluar (termasuk belanja stok)
                                </div>
                            </div>
                            <div class="col-auto text-right">
                                <div class="small text-muted mb-1">Saldo Awal Bulan</div>
                                <div class="font-weight-bold text-gray-700">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</div>
                                <div class="small text-muted mt-2 mb-1">Saldo Akhir Bulan</div>
                                <div class="font-weight-bold" style="color:#36b9cc">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Arus Kas Harian -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">📅 Rincian Arus Kas Harian</h6>
                    </div>
                    <div class="card-body p-0">
                        @if($arusKasHarian->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th class="text-right">Masuk</th>
                                        <th class="text-right">Keluar</th>
                                        <th class="text-right">Saldo Berjalan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($arusKasHarian as $row)
                                    <tr>
                                        <td class="small">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                        <td class="text-right small text-success">Rp {{ number_format($row->masuk, 0, ',', '.') }}</td>
                                        <td class="text-right small text-danger">Rp {{ number_format($row->keluar, 0, ',', '.') }}</td>
                                        <td class="text-right small font-weight-bold">Rp {{ number_format($row->saldo_berjalan, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-wallet fa-3x mb-3 d-block"></i>
                            Belum ada data transaksi di bulan ini
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Grafik Harian -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">📊 Grafik Pemasukan & Pengeluaran Harian</h6>
                    </div>
                    <div class="card-body">
                        @if($harian->count() > 0)
                        <div class="chart-area">
                            <canvas id="harianChart"></canvas>
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-bar fa-3x mb-3 d-block"></i>
                            Belum ada data transaksi di bulan ini
                        </div>
                        @endif
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

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script>
@if($harian->count() > 0)
var labels = {!! json_encode($harian->pluck('tanggal')->map(fn($t) => \Carbon\Carbon::parse($t)->format('d M'))) !!};
var masuk  = {!! json_encode($harian->pluck('masuk')) !!};
var keluar = {!! json_encode($harian->pluck('keluar')) !!};

new Chart(document.getElementById('harianChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Pemasukan',
            data: masuk,
            backgroundColor: 'rgba(28,200,138,0.7)',
            borderColor: '#1cc88a',
            borderWidth: 1
        }, {
            label: 'Pengeluaran',
            data: keluar,
            backgroundColor: 'rgba(231,74,59,0.7)',
            borderColor: '#e74a3b',
            borderWidth: 1
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            yAxes: [{
                ticks: {
                    callback: function(val) {
                        return 'Rp ' + (val/1000) + 'k';
                    }
                }
            }]
        },
        tooltips: {
            callbacks: {
                label: function(item) {
                    return item.dataset + ': Rp ' + parseInt(item.value).toLocaleString('id-ID');
                }
            }
        }
    }
});
@endif
</script>
</body>
</html>
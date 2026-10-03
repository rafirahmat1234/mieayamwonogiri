<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Pengeluaran - Mie Ayam Wonogiri</title>
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .amount-out { color:#e74a3b; font-weight:700; }
        .badge-keluar { background:#e74a3b; color:#fff; padding:4px 12px; border-radius:20px; font-size:11px; }
        .border-left-red { border-left: 4px solid #e74a3b !important; }
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
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span>
            </a>
        </li>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Keuangan</div>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('pemasukan.index') }}">
                <i class="fas fa-fw fa-arrow-up"></i><span>Pemasukan</span>
            </a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" href="{{ route('pengeluaran.index') }}" style="background:rgba(231,74,59,0.2)">
                <i class="fas fa-fw fa-arrow-down"></i><span>Pengeluaran</span>
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
                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small font-weight-bold">{{ Auth::user()->name }}</span>
                            <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:14px">
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

                <!-- Header -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-danger">
                        <i class="fas fa-arrow-down mr-2"></i>Pengeluaran
                    </h1>
                    <button class="btn btn-danger btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-plus fa-sm mr-1"></i> Tambah Pengeluaran
                    </button>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                @endif

                <!-- Filter -->
                <div class="card shadow mb-4">
                    <div class="card-body py-3">
                        <form method="GET" action="{{ route('pengeluaran.index') }}">
                            <input type="hidden" name="periode" id="inputPeriode" value="{{ $filterPeriode }}">
                            <div class="row align-items-end">
                                <div class="col-md-4 mb-2">
                                    <label class="font-weight-bold small d-block mb-1">Filter Periode</label>
                                    <div class="btn-group btn-group-sm w-100">
                                        <button type="button" onclick="setPeriode('hari')" class="btn {{ $filterPeriode=='hari' ? 'btn-danger' : 'btn-outline-danger' }}" id="btn-hari">📅 Harian</button>
                                        <button type="button" onclick="setPeriode('bulan')" class="btn {{ $filterPeriode=='bulan' ? 'btn-danger' : 'btn-outline-danger' }}" id="btn-bulan">📆 Bulanan</button>
                                        <button type="button" onclick="setPeriode('tahun')" class="btn {{ $filterPeriode=='tahun' ? 'btn-danger' : 'btn-outline-danger' }}" id="btn-tahun">🗓 Tahunan</button>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div id="filter-hari" style="display:none">
                                        <label class="font-weight-bold small d-block mb-1">Pilih Tanggal</label>
                                        <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ $tanggal }}">
                                    </div>
                                    <div id="filter-bulan" style="display:none">
                                        <label class="font-weight-bold small d-block mb-1">Pilih Bulan</label>
                                        <input type="month" name="bulan" class="form-control form-control-sm" value="{{ $bulan }}">
                                    </div>
                                    <div id="filter-tahun" style="display:none">
                                        <label class="font-weight-bold small d-block mb-1">Pilih Tahun</label>
                                        <select name="tahun" class="form-control form-control-sm">
                                            @for($y = date('Y'); $y >= 2020; $y--)
                                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <button type="submit" class="btn btn-danger btn-sm btn-block">
                                        <i class="fas fa-filter mr-1"></i> Tampilkan
                                    </button>
                                    <a href="{{ route('pengeluaran.index') }}" class="btn btn-secondary btn-sm btn-block mt-1">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Total Card -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-left-red shadow py-2">
                            <div class="card-body py-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran</div>
                                <div class="h4 mb-0 font-weight-bold text-danger">
                                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-info shadow py-2">
                            <div class="card-body py-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah Transaksi</div>
                                <div class="h4 mb-0 font-weight-bold text-info">
                                    {{ $transaksi->count() }} Transaksi
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-warning shadow py-2">
                            <div class="card-body py-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Rata-rata per Transaksi</div>
                                <div class="h4 mb-0 font-weight-bold text-warning">
                                    Rp {{ $transaksi->count() > 0 ? number_format($totalKeluar / $transaksi->count(), 0, ',', '.') : 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#e74a3b,#c0392b)">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-arrow-down mr-2"></i>Daftar Pengeluaran
                        </h6>
                        <span class="badge badge-light text-danger">{{ $transaksi->count() }} data</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th class="text-right">Jumlah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transaksi as $i => $t)
                                    <tr>
                                        <td class="text-muted small">{{ $i + 1 }}</td>
                                        <td class="small">{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</td>
                                        <td>{{ $t->keterangan }}</td>
                                        <td class="text-right amount-out">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                                        <td>
                                            <form action="{{ route('transaksi.destroy', $t->id) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Hapus pengeluaran ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block text-danger" style="opacity:0.3"></i>
                                            Belum ada data pengeluaran.<br>
                                            <button class="btn btn-danger btn-sm mt-2" data-toggle="modal" data-target="#modalTambah">+ Tambah Pengeluaran</button>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
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

<!-- Modal Tambah Pengeluaran -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('transaksi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tipe" value="pengeluaran">
                <div class="modal-header" style="background:linear-gradient(135deg,#e74a3b,#c0392b)">
                    <h5 class="modal-title font-weight-bold text-white">
                        <i class="fas fa-arrow-down mr-2"></i>Tambah Pengeluaran
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold small">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold small">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Beli bahan baku" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold small">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" class="form-control" placeholder="0" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold small">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-save mr-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
<script>
function setPeriode(p) {
    document.getElementById('inputPeriode').value = p;
    ['hari','bulan','tahun'].forEach(function(x) {
        document.getElementById('filter-' + x).style.display = x === p ? 'block' : 'none';
        var btn = document.getElementById('btn-' + x);
        btn.className = btn.className.replace('btn-danger','btn-outline-danger');
        if (x === p) btn.className = btn.className.replace('btn-outline-danger','btn-danger');
    });
}
setPeriode('{{ $filterPeriode }}');
</script>
</body>
</html>
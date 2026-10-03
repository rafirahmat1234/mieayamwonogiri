<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Transaksi - Mie Ayam Wonogiri</title>
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .badge-masuk   { background:#1cc88a; color:#fff; padding:4px 10px; border-radius:20px; font-size:11px; }
        .badge-keluar  { background:#e74a3b; color:#fff; padding:4px 10px; border-radius:20px; font-size:11px; }
        .amount-in     { color:#1cc88a; font-weight:700; }
        .amount-out    { color:#e74a3b; font-weight:700; }
    </style>
</head>
<body id="page-top">
<div id="wrapper">

    <!-- SIDEBAR -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
            <div class="sidebar-brand-icon mr-2">🍜</div>
            <div class="sidebar-brand-text">Mie Ayam Wonogiri</div>
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span>
            </a>
        </li>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Keuangan</div>
        <li class="nav-item active">
            <a class="nav-link" href="{{ route('transaksi.index') }}">
                <i class="fas fa-fw fa-exchange-alt"></i><span>Transaksi</span>
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
                    <h1 class="h3 mb-0 text-gray-800">📝 Transaksi Keuangan</h1>
                    <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-plus fa-sm mr-1"></i> Tambah Transaksi
                    </button>
                </div>

                <!-- Alert -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                @endif

                <!-- Filter -->
                <div class="card shadow mb-3">
                    <div class="card-body py-2">
                        <form method="GET" action="{{ route('transaksi.index') }}" class="form-inline">
                            <label class="mr-2 font-weight-bold small">Filter Bulan:</label>
                            <input type="month" name="bulan" class="form-control form-control-sm mr-2"
                                value="{{ request('bulan', date('Y-m')) }}">
                            <select name="tipe" class="form-control form-control-sm mr-2">
                                <option value="">Semua Tipe</option>
                                <option value="pemasukan" {{ request('tipe')=='pemasukan' ? 'selected' : '' }}>↑ Pemasukan</option>
                                <option value="pengeluaran" {{ request('tipe')=='pengeluaran' ? 'selected' : '' }}>↓ Pengeluaran</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                            <a href="{{ route('transaksi.index') }}" class="btn btn-secondary btn-sm ml-2">Reset</a>
                        </form>
                    </div>
                </div>

                <!-- Ringkasan Bulan Ini -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card border-left-success shadow py-2">
                            <div class="card-body py-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pemasukan</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-danger shadow py-2">
                            <div class="card-body py-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-warning shadow py-2">
                            <div class="card-body py-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Laba Bersih</div>
                                <div class="h6 mb-0 font-weight-bold {{ ($totalMasuk - $totalKeluar) >= 0 ? 'text-success' : 'text-danger' }}">
                                    Rp {{ number_format($totalMasuk - $totalKeluar, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Transaksi -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi</h6>
                        <span class="badge badge-primary">{{ $transaksi->count() }} transaksi</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Tipe</th>
                                        <th>Kategori</th>
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
                                        <td>
                                            @if($t->tipe == 'pemasukan')
                                                <span class="badge-masuk">↑ Masuk</span>
                                            @else
                                                <span class="badge-keluar">↓ Keluar</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $t->kategori->nama ?? '-' }}</td>
                                        <td>{{ $t->keterangan }}</td>
                                        <td class="text-right {{ $t->tipe == 'pemasukan' ? 'amount-in' : 'amount-out' }}">
                                            Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <form action="{{ route('transaksi.destroy', $t->id) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Hapus transaksi ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-receipt fa-2x mb-2 d-block"></i>
                                            Belum ada transaksi. Tambah transaksi dulu!
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

<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('transaksi.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-plus mr-2"></i>Tambah Transaksi</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <!-- Tipe -->
                    <div class="form-group">
                        <label class="font-weight-bold small">Jenis Transaksi</label>
                        <div class="d-flex gap-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipe" id="tipeMasuk" value="pemasukan" checked>
                                <label class="form-check-label text-success font-weight-bold" for="tipeMasuk">↑ Pemasukan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipe" id="tipeKeluar" value="pengeluaran">
                                <label class="form-check-label text-danger font-weight-bold" for="tipeKeluar">↓ Pengeluaran</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold small">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold small">Kategori</label>
                                <select name="kategori_id" class="form-control">
                                    <option value="">— Pilih Kategori —</option>
                                    @foreach($kategoris as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold small">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control"
                            placeholder="Contoh: Penjualan mie ayam pagi" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold small">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" class="form-control"
                            placeholder="0" min="0" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold small">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control" rows="2"
                            placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Stok Bahan - Mie Ayam Wonogiri</title>
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .progress { height: 8px; }
        .stok-habis { color: #e74a3b; font-weight: 700; }
        .stok-aman  { color: #1cc88a; font-weight: 700; }
        .stok-warn  { color: #f6c23e; font-weight: 700; }
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
<li class="nav-item active">
    <a class="nav-link" href="{{ route('stok-bahan.index') }}">
        <i class="fas fa-fw fa-boxes"></i><span>Stok Bahan</span>
    </a>
</li>
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
                    <h1 class="h3 mb-0 text-gray-800">📦 Stok Bahan Baku</h1>
                    @if(Auth::user()->role === 'owner')
                    <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-plus fa-sm mr-1"></i> Tambah Bahan
                    </button>
                    @else
                    <span class="badge badge-secondary p-2">🔒 Mode Admin — Read Only</span>
                    @endif
                </div>

                <!-- Alert -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <i class="fas fa-lock mr-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                @endif

                <!-- Warning Stok Habis -->
                @php $stokHabis = $stok->filter(fn($s) => $s->stok_saat_ini <= $s->stok_minimum); @endphp
                @if($stokHabis->count() > 0)
                <div class="alert alert-danger shadow-sm">
                    <strong><i class="fas fa-exclamation-triangle mr-2"></i>Perhatian!</strong>
                    {{ $stokHabis->count() }} bahan hampir/sudah habis:
                    <strong>{{ $stokHabis->pluck('nama_bahan')->join(', ') }}</strong>
                    — Segera restock!
                </div>
                @endif

                <!-- Tabel Stok -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Stok Bahan</h6>
                        <span class="badge badge-primary">{{ $stok->count() }} bahan</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Bahan</th>
                                        <th>Satuan</th>
                                        <th>Stok Saat Ini</th>
                                        <th>Stok Min.</th>
                                        <th>Harga/Satuan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stok as $i => $s)
                                    @php
                                        $pct = $s->stok_minimum > 0
                                            ? min(100, ($s->stok_saat_ini / ($s->stok_minimum * 3)) * 100)
                                            : 50;
                                        $barColor = $s->stok_saat_ini <= $s->stok_minimum
                                            ? 'bg-danger'
                                            : ($pct < 60 ? 'bg-warning' : 'bg-success');
                                        $statusClass = $s->stok_saat_ini <= $s->stok_minimum
                                            ? 'stok-habis'
                                            : ($pct < 60 ? 'stok-warn' : 'stok-aman');
                                        $statusText = $s->stok_saat_ini <= $s->stok_minimum
                                            ? '⚠ Hampir Habis'
                                            : ($pct < 60 ? '⚡ Perlu Restock' : '✓ Aman');
                                    @endphp
                                    <tr>
                                        <td class="text-muted small">{{ $i + 1 }}</td>
                                        <td class="font-weight-bold">{{ $s->nama_bahan }}</td>
                                        <td class="text-muted">{{ $s->satuan }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="{{ $statusClass }} mr-2">{{ $s->stok_saat_ini + 0 }}</span>
                                                <div class="progress flex-grow-1" style="min-width:80px">
                                                    <div class="progress-bar {{ $barColor }}" style="width:{{ $pct }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $s->stok_minimum + 0 }} {{ $s->satuan }}</td>
                                        <td>Rp {{ number_format($s->harga_per_satuan, 0, ',', '.') }}</td>
                                        <td><span class="{{ $statusClass }}">{{ $statusText }}</span></td>
                                        <td>
                                        @if(Auth::user()->role === 'owner')
                                        <button class="btn btn-warning btn-sm"
                                            data-toggle="modal"
                                            data-target="#modalUpdate"
                                            data-id="{{ $s->id }}"
                                            data-nama="{{ $s->nama_bahan }}"
                                            data-stok="{{ $s->stok_saat_ini }}"
                                            data-satuan="{{ $s->satuan }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('stok-bahan.destroy', $s->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Hapus {{ $s->nama_bahan }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-muted"><i class="fas fa-lock"></i> Terkunci</span>
                                        @endif
                                    </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                                            Belum ada data stok. Tambah bahan baku dulu!
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

<!-- Modal Tambah Bahan -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('stok-bahan.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-plus mr-2"></i>Tambah Bahan Baku</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold small">Nama Bahan</label>
                        <input type="text" name="nama_bahan" class="form-control" placeholder="Contoh: Mie Basah" required>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold small">Satuan</label>
                                <input type="text" name="satuan" class="form-control" placeholder="kg / botol / ikat" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold small">Stok Awal</label>
                                <input type="number" name="stok_saat_ini" class="form-control" placeholder="0" min="0" step="0.5" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold small">Stok Minimum</label>
                                <input type="number" name="stok_minimum" class="form-control" placeholder="0" min="0" step="0.5" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold small">Harga per Satuan (Rp)</label>
                                <input type="number" name="harga_per_satuan" class="form-control" placeholder="0" min="0" required>
                            </div>
                        </div>
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

<!-- Modal Update Stok -->
<div class="modal fade" id="modalUpdate" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="formUpdate" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Update Stok</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="font-weight-bold mb-3" id="modalNamaBahan"></p>
                    <div class="form-group">
                        <label class="font-weight-bold small">Stok Saat Ini <span id="modalSatuan" class="text-muted"></span></label>
                        <input type="number" name="stok_saat_ini" id="modalStokInput" class="form-control form-control-lg" min="0" step="0.5" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Update</button>
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
$('#modalUpdate').on('show.bs.modal', function(e) {
    var btn = $(e.relatedTarget);
    $('#modalNamaBahan').text(btn.data('nama'));
    $('#modalStokInput').val(btn.data('stok'));
    $('#modalSatuan').text('(' + btn.data('satuan') + ')');
    $('#formUpdate').attr('action', '/stok-bahan/' + btn.data('id'));
});
</script>
</body>
</html>
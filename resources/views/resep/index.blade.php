<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Resep Menu - Mie Ayam Wonogiri</title>
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">

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
            <a class="nav-link" href="{{ route('pos.index') }}">
                <i class="fas fa-fw fa-cash-register"></i><span>Kasir (POS)</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('laporan.index') }}">
                <i class="fas fa-fw fa-chart-bar"></i><span>Laporan</span>
            </a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" href="{{ route('resep.index') }}">
                <i class="fas fa-fw fa-utensils"></i><span>Resep Menu</span>
            </a>
        </li>
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
                    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-utensils mr-2"></i>Resep Menu</h1>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                @endif

                <div class="alert alert-info shadow-sm">
                    <i class="fas fa-info-circle mr-2"></i>
                    Setiap menu harus diatur resepnya (bahan apa &amp; jumlahnya per porsi) supaya stok bahan
                    bisa otomatis berkurang saat ada pesanan masuk dari POS. Menu yang belum diatur tetap
                    bisa dipesan, tapi stok bahan tidak akan berkurang untuk menu itu.
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Menu</h6>
                        <span class="badge badge-primary">{{ $menus->count() }} menu</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Menu</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Status Resep</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($menus as $i => $menu)
                                    @php $jumlah = $jumlahResepPerMenu[$menu->id] ?? 0; @endphp
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="font-weight-bold">{{ $menu->nama_menu }}</td>
                                        <td>{{ $menu->kategori }}</td>
                                        <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($jumlah > 0)
                                                <span class="text-success"><i class="fas fa-fw fa-check-circle"></i> {{ $jumlah }} bahan</span>
                                            @else
                                                <span class="text-danger"><i class="fas fa-fw fa-exclamation-triangle"></i> Belum diatur</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('resep.edit', $menu->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-fw fa-edit"></i> Atur Resep
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada menu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
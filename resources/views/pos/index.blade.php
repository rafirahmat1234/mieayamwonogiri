<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kasir POS - Mie Ayam Wonogiri</title>
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .menu-card { cursor: pointer; transition: transform 0.1s; }
        .menu-card:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .cart-item { border-bottom: 1px solid #eee; padding: 8px 0; }
        .qty-btn { width: 28px; height: 28px; padding: 0; }
        #cartTotal { font-size: 1.5rem; font-weight: 700; color: #1cc88a; }
    </style>
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
        <li class="nav-item active">
            <a class="nav-link" href="{{ route('pos.index') }}">
                <i class="fas fa-fw fa-cash-register"></i><span>Kasir (POS)</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('laporan.index') }}">
                <i class="fas fa-fw fa-chart-bar"></i><span>Laporan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('resep.index') }}">
                <i class="fas fa-fw fa-utensils"></i><span>Resep Menu</span>
            </a>
        </li>
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
                    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-cash-register mr-2"></i>Kasir (POS)</h1>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                @endif

                <div class="row">
                    <!-- Daftar Menu -->
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Pilih Menu</h6>
                            </div>
                            <div class="card-body">
                                <div class="row" id="menuList">
                                    @forelse($menus as $menu)
                                    <div class="col-md-4 col-6 mb-3">
                                        <div class="card menu-card shadow-sm"
                                             onclick="tambahKeKeranjang({{ $menu->id }}, '{{ $menu->nama_menu }}', {{ $menu->harga }})">
                                            <div class="card-body text-center py-3">
                                                <div class="font-weight-bold small">{{ $menu->nama_menu }}</div>
                                                <div class="text-success font-weight-bold">
                                                    Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="col-12 text-center text-muted py-4">
                                        <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                                        Belum ada menu. Tambah menu dulu di halaman Kelola Menu.
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keranjang -->
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-shopping-cart mr-1"></i> Keranjang
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="cartItems">
                                    <p class="text-muted text-center small" id="cartEmpty">Keranjang masih kosong</p>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="font-weight-bold">Total</span>
                                    <span id="cartTotal">Rp 0</span>
                                </div>
                                <form id="formPesanan" action="{{ route('pos.store') }}" method="POST">
                                    @csrf
                                    <div id="hiddenInputs"></div>
                                    <button type="submit" class="btn btn-primary btn-block" id="btnSimpan" disabled>
                                        <i class="fas fa-check mr-1"></i> Simpan Pesanan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
let cart = {}; // { menu_id: { nama, harga, jumlah } }

function tambahKeKeranjang(menuId, nama, harga) {
    if (cart[menuId]) {
        cart[menuId].jumlah += 1;
    } else {
        cart[menuId] = { nama: nama, harga: harga, jumlah: 1 };
    }
    renderCart();
}

function ubahJumlah(menuId, delta) {
    if (!cart[menuId]) return;
    cart[menuId].jumlah += delta;
    if (cart[menuId].jumlah <= 0) {
        delete cart[menuId];
    }
    renderCart();
}

function formatRupiah(angka) {
    return 'Rp ' + angka.toLocaleString('id-ID');
}

function renderCart() {
    const cartItemsEl = document.getElementById('cartItems');
    const cartEmptyEl = document.getElementById('cartEmpty');
    const hiddenInputsEl = document.getElementById('hiddenInputs');
    const btnSimpan = document.getElementById('btnSimpan');

    const keys = Object.keys(cart);

    if (keys.length === 0) {
        cartItemsEl.innerHTML = '<p class="text-muted text-center small" id="cartEmpty">Keranjang masih kosong</p>';
        document.getElementById('cartTotal').textContent = formatRupiah(0);
        btnSimpan.disabled = true;
        hiddenInputsEl.innerHTML = '';
        return;
    }

    let html = '';
    let total = 0;
    let hiddenHtml = '';

    keys.forEach((menuId, index) => {
        const item = cart[menuId];
        const subtotal = item.harga * item.jumlah;
        total += subtotal;

        html += `
            <div class="cart-item">
                <div class="d-flex justify-content-between">
                    <span class="small font-weight-bold">${item.nama}</span>
                    <span class="small">${formatRupiah(subtotal)}</span>
                </div>
                <div class="d-flex align-items-center mt-1">
                    <button type="button" class="btn btn-outline-secondary btn-sm qty-btn" onclick="ubahJumlah(${menuId}, -1)">-</button>
                    <span class="mx-2">${item.jumlah}</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm qty-btn" onclick="ubahJumlah(${menuId}, 1)">+</button>
                </div>
            </div>
        `;

        hiddenHtml += `
            <input type="hidden" name="items[${index}][menu_id]" value="${menuId}">
            <input type="hidden" name="items[${index}][jumlah]" value="${item.jumlah}">
        `;
    });

    cartItemsEl.innerHTML = html;
    hiddenInputsEl.innerHTML = hiddenHtml;
    document.getElementById('cartTotal').textContent = formatRupiah(total);
    btnSimpan.disabled = false;
}
</script>
</body>
</html>
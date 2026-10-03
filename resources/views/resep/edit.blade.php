<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Atur Resep - Mie Ayam Wonogiri</title>
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
                    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-utensils mr-2"></i>Atur Resep: {{ $menu->nama_menu }}</h1>
                </div>

                <p class="text-muted">Centang bahan yang dipakai untuk 1 porsi menu ini, lalu isi jumlahnya (sesuai satuan di halaman Stok Bahan).</p>

                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="alertBahanBaru"></div>

                {{-- ====== KARTU TAMBAH BAHAN BARU (collapsible) ====== --}}
                <div class="card shadow mb-4">
                    <a href="#formBahanBaru" data-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="formBahanBaru"
                       class="card-header py-3 d-flex align-items-center justify-content-between text-decoration-none">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Bahan Baru
                        </h6>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="collapse" id="formBahanBaru">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <label class="small mb-1">Nama Bahan</label>
                                    <input type="text" id="inputNamaBahan" class="form-control form-control-sm" placeholder="misal: kecap manis">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="small mb-1">Satuan</label>
                                    <input type="text" id="inputSatuan" class="form-control form-control-sm" placeholder="misal: botol, kg, gr, pcs">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="small mb-1">Stok Awal</label>
                                    <input type="number" step="0.01" min="0" id="inputStokAwal" class="form-control form-control-sm" placeholder="0">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="small mb-1">Stok Minimum</label>
                                    <input type="number" step="0.01" min="0" id="inputStokMin" class="form-control form-control-sm" placeholder="0">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="small mb-1">Harga / Satuan</label>
                                    <input type="number" step="0.01" min="0" id="inputHarga" class="form-control form-control-sm" placeholder="0">
                                </div>
                                <div class="col-md-1 mb-2 d-flex align-items-end">
                                    <button type="button" id="btnSimpanBahanBaru" class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Bahan baru otomatis muncul di tabel di bawah, langsung bisa dicentang & diisi jumlahnya.</small>
                        </div>
                    </div>
                </div>
                {{-- ====== END KARTU TAMBAH BAHAN BARU ====== --}}

                <form action="{{ route('resep.update', $menu->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Bahan</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                            <table class="table mb-0" id="tabelBahan">
                                <thead>
                                    <tr>
                                        <th style="width:70px">Pakai</th>
                                        <th>Nama Bahan</th>
                                        <th>Satuan</th>
                                        <th style="width:200px">Jumlah per Porsi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyBahan">
                                    @forelse ($stokBahanList as $bahan)
                                        @php $jumlahSaatIni = $resepSaatIni[$bahan->id] ?? null; @endphp
                                        <tr>
                                            <td>
                                                <input type="checkbox"
                                                       name="bahan[{{ $bahan->id }}][dipakai]"
                                                       value="1"
                                                       {{ $jumlahSaatIni !== null ? 'checked' : '' }}>
                                            </td>
                                            <td>{{ $bahan->nama_bahan }}</td>
                                            <td>{{ $bahan->satuan }}</td>
                                            <td>
                                                <input type="number"
                                                       step="0.01"
                                                       min="0"
                                                       class="form-control form-control-sm"
                                                       name="bahan[{{ $bahan->id }}][jumlah]"
                                                       value="{{ $jumlahSaatIni ?? '' }}"
                                                       placeholder="0.00">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="rowKosong">
                                            <td colspan="4" class="text-center text-muted py-3">
                                                Belum ada data di Stok Bahan. Tambahkan dulu lewat form di atas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-fw fa-save"></i> Simpan Resep</button>
                    <a href="{{ route('resep.index') }}" class="btn btn-secondary">Batal</a>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<script>
document.getElementById('btnSimpanBahanBaru').addEventListener('click', function () {
    const btn = this;
    const namaBahan = document.getElementById('inputNamaBahan').value.trim();
    const satuan = document.getElementById('inputSatuan').value.trim();
    const stokAwal = document.getElementById('inputStokAwal').value;
    const stokMin = document.getElementById('inputStokMin').value;
    const harga = document.getElementById('inputHarga').value;
    const alertBox = document.getElementById('alertBahanBaru');

    alertBox.innerHTML = '';

    if (!namaBahan || !satuan) {
        alertBox.innerHTML = '<div class="alert alert-warning shadow-sm">Nama bahan dan satuan wajib diisi.</div>';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch("{{ route('resep.bahan-baru') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            nama_bahan: namaBahan,
            satuan: satuan,
            stok_saat_ini: stokAwal || 0,
            stok_minimum: stokMin || 0,
            harga_per_satuan: harga || 0,
        }),
    })
    .then(async (response) => {
        const data = await response.json();
        if (!response.ok) {
            throw { status: response.status, data };
        }
        return data;
    })
    .then((data) => {
        const bahan = data.bahan;

        // Hilangkan baris "Belum ada data" kalau masih ada
        const rowKosong = document.getElementById('rowKosong');
        if (rowKosong) rowKosong.remove();

        // Sisipkan baris baru ke tabel, langsung tercentang
        const tbody = document.getElementById('tbodyBahan');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="checkbox" name="bahan[${bahan.id}][dipakai]" value="1" checked>
            </td>
            <td>${bahan.nama_bahan}</td>
            <td>${bahan.satuan}</td>
            <td>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                       name="bahan[${bahan.id}][jumlah]" placeholder="0.00">
            </td>
        `;
        tbody.appendChild(tr);

        // Kosongkan form & kasih notifikasi sukses
        document.getElementById('inputNamaBahan').value = '';
        document.getElementById('inputSatuan').value = '';
        document.getElementById('inputStokAwal').value = '';
        document.getElementById('inputStokMin').value = '';
        document.getElementById('inputHarga').value = '';

        alertBox.innerHTML = `<div class="alert alert-success shadow-sm">Bahan "${bahan.nama_bahan}" berhasil ditambahkan. Silakan isi jumlah per porsi di tabel bawah.</div>`;
        document.getElementById('inputNamaBahan').focus();
    })
    .catch((err) => {
        let pesan = 'Gagal menambahkan bahan. Coba lagi.';
        if (err.data && err.data.errors) {
            pesan = Object.values(err.data.errors).flat().join(' ');
        } else if (err.data && err.data.message) {
            pesan = err.data.message;
        }
        alertBox.innerHTML = `<div class="alert alert-danger shadow-sm">${pesan}</div>`;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i>';
    });
});
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator HPP Premium - GuBah</title>
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --brand-orange: #FFA500;
            --brand-orange-hover: #e69500;
        }
        .bg-orange { background-color: var(--brand-orange) !important; color: #ffffff !important; }
        .text-orange { color: var(--brand-orange) !important; }
        .btn-orange { background-color: var(--brand-orange); color: #ffffff; border: none; }
        .btn-orange:hover { background-color: var(--brand-orange-hover); color: #ffffff; }
        .card-custom { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        [data-bs-theme="dark"] .card-custom { border: 1px solid #2d3238; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); }
        .table-custom th { background-color: rgba(255, 165, 0, 0.1); color: var(--brand-orange); }
    </style>
</head>
<body class="bg-body-tertiary">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-body border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <span class="badge bg-orange rounded-3 p-2 fs-6">🚀 GuBah</span>
            <span class="d-none d-sm-inline fs-5">Kalkulator HPP</span>
        </a>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-warning text-dark border"><i class="bi bi-star-fill"></i> Akun Premium</span>
            <button class="btn btn-outline-secondary rounded-pill px-3 d-flex align-items-center gap-2" id="btnDarkMode">
                <i class="bi bi-moon-stars-fill" id="darkModeIcon"></i>
            </button>
        </div>
    </div>
</nav>

<div class="container py-4 mb-5">
    
    <div class="text-center mb-4">
        <h2 class="fw-bold text-orange"><i class="bi bi-calculator-fill"></i> Kalkulator Harga Pokok Penjualan (HPP)</h2>
        <p class="text-body-secondary">Hitung biaya produksi secara akurat dan tentukan harga jual yang menguntungkan.</p>
    </div>

    <div class="row g-4">
        <!-- KOLOM KIRI: FORM INPUT -->
        <div class="col-lg-7">
            <div class="card card-custom p-4 bg-body">
                
                @if($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('premium.hpp.hitung') }}" method="POST" id="hppForm">
                    @csrf
                    
                    <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-basket-fill text-orange"></i> 1. Rincian Bahan Baku</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless table-custom align-middle" id="tableBahan">
                            <thead>
                                <tr>
                                    <th>Nama Bahan</th>
                                    <th>Harga Beli (Rp)</th>
                                    <th>Qty Beli (g/ml)</th>
                                    <th>Qty Dipakai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyBahan">
                                @if(isset($aiBahan) && count($aiBahan) > 0)
                                    <!-- Jika ada lemparan data dari AI, cetak baris otomatis -->
                                    @foreach($aiBahan as $index => $itemBahan)
                                    <tr>
                                        <!-- Kolom Nama Bahan -->
                                        <td>
                                            <input type="text" name="bahan[{{ $index }}][nama]" class="form-control fw-bold" value="{{ is_array($itemBahan) ? ($itemBahan['nama'] ?? '') : $itemBahan }}" readonly>
                                            @if(is_array($itemBahan) && !empty($itemBahan['takaran']))
                                                <div class="form-text text-orange small mt-1"><i class="bi bi-info-circle"></i> {{ $itemBahan['takaran'] }}</div>
                                            @endif
                                        </td>
                                        
                                        <!-- Kolom Harga Beli Kemasan -->
                                        <td>
                                            <input type="number" name="bahan[{{ $index }}][harga_beli]" class="form-control harga-beli" placeholder="Misal: 15000" min="0" required>
                                        </td>
                                        
                                        <!-- Kolom Qty Beli -->
                                        <td>
                                            <input type="number" name="bahan[{{ $index }}][qty_beli]" class="form-control qty-beli" placeholder="Misal: 1000 (gr) / 1 (pcs)" min="1" required>
                                            <div class="form-text small text-muted">Bisa dlm gr, ml, atau butir/pcs.</div>
                                        </td>
                                        
                                        <!-- Kolom Qty Pakai -->
                                        <td>
                                            <input type="number" name="bahan[{{ $index }}][qty_pakai]" class="form-control qty-pakai" value="{{ is_array($itemBahan) ? ($itemBahan['gram'] ?? '') : '' }}" placeholder="Pemakaian" min="1" required>
                                            <div class="form-text small text-muted">Samakan satuannya dgn kolom sblmnya.</div>
                                        </td>
                                        
                                        <!-- Tombol Hapus Baris -->
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm hapus-baris"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <!-- Jika dikunjungi langsung tanpa AI, cetak 1 baris kosong default -->
                                    <tr>
                                        <td><input type="text" name="bahan[0][nama]" class="form-control" placeholder="Contoh: Tepung" required></td>
                                        <td><input type="number" name="bahan[0][harga_beli]" class="form-control" placeholder="15000" min="0" required></td>
                                        <td><input type="number" name="bahan[0][qty_beli]" class="form-control" placeholder="1000" min="1" required></td>
                                        <td><input type="number" name="bahan[0][qty_pakai]" class="form-control" placeholder="250" min="0" required></td>
                                        <td><button type="button" class="btn btn-danger btn-sm hapus-baris" disabled><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                @endif
                            </tbody>
                            <div class="alert alert-danger py-2 mb-3 small border-0 shadow-sm" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>PERHATIAN:</strong> Pastikan isian <b>Isi Kemasan (Qty Beli)</b> dan <b>Pemakaian</b> menggunakan satuan ukur yang SAMA (sama-sama gram, sama-sama ml, atau sama-sama butir).
                            </div>
                            <div class="row mt-4 align-items-center bg-body-tertiary p-4 rounded-4 border">
                                <!-- Input Jumlah Porsi -->
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-orange">
                                        <i class="bi bi-pie-chart-fill"></i> Resep Ini Menjadi Berapa Porsi/Pcs?
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <!-- PENTING: id="inputPorsi" -->
                                        <input type="number" id="inputPorsi" class="form-control fw-bold text-center" value="1" min="1">
                                        <span class="input-group-text">Pcs/Porsi</span>
                                    </div>
                                    <div class="form-text mt-2">Membagi total bahan menjadi HPP satuan.</div>
                                </div>

                                <!-- Tampilan Angka Real-Time -->
                                <div class="col-md-4 mb-3 mb-md-0 text-center">
                                    <p class="text-body-secondary mb-1">Total Biaya 1 Resep Penuh</p>
                                    <!-- PENTING: id="tampilTotalResep" -->
                                    <h4 class="fw-bold text-danger mb-0" id="tampilTotalResep">Rp 0</h4>
                                    
                                    <hr class="w-50 mx-auto my-2">
                                    
                                    <p class="text-body-secondary mb-1 fw-bold">HPP Murni Per Pcs/Porsi</p>
                                    <!-- PENTING: id="tampilHppPcs" -->
                                    <h3 class="fw-bold text-success mb-0" id="tampilHppPcs">Rp 0</h3>
                                </div>

                                <!-- Saran Harga Jual Per Porsi -->
                                <div class="col-md-4 text-center border-start border-warning border-3">
                                    <p class="text-body-secondary mb-1">Saran Harga Jual Per Pcs</p>
                                    <!-- PENTING: id="tampilHargaJualPcs" -->
                                    <h2 class="fw-bold text-orange mb-1" id="tampilHargaJualPcs">Rp 0</h2>
                                    <span class="badge bg-warning text-dark rounded-pill px-3">Sudah termasuk untung</span>
                                </div>
                            </div>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-4" id="btnTambahBahan">
                        <i class="bi bi-plus-circle"></i> Tambah Bahan Baku
                    </button>

                    <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-lightning-charge-fill text-orange"></i> 2. Biaya Operasional & Margin</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Biaya Operasional (Gas, Kemasan, dll)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="biaya_operasional" class="form-control" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Target Margin Keuntungan</label>
                            <div class="input-group">
                                <input type="number" name="margin" class="form-control" value="40" min="0" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-orange w-100 fw-bold py-3 fs-5 rounded-3">
                        <i class="bi bi-check2-circle"></i> Kalkulasi Sekarang
                    </button>
                </form>
            </div>
        </div>

        <!-- KOLOM KANAN: HASIL KALKULASI -->
        <div class="col-lg-5">
            @if(session('hasil_hpp'))
                @php $hasil = session('hasil_hpp'); @endphp
                <div class="card card-custom p-4 bg-body border-top border-5 border-success shadow">
                    <h4 class="fw-bold text-success mb-3"><i class="bi bi-receipt"></i> Hasil Analisis Keuangan</h4>
                    
                    <div class="bg-body-tertiary p-3 rounded-3 mb-3 border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-body-secondary">Total Biaya Bahan:</span>
                            <span class="fw-semibold">Rp {{ number_format($hasil['total_biaya_bahan'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-body-secondary">Biaya Operasional:</span>
                            <span class="fw-semibold">Rp {{ number_format($hasil['biaya_operasional'], 0, ',', '.') }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold fs-5 text-orange">
                            <span>Total HPP:</span>
                            <span>Rp {{ number_format($hasil['hpp_total'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="bg-warning-subtle text-warning-emphasis p-3 rounded-3 mb-4 border border-warning-subtle">
                        <h6 class="fw-bold mb-2">Target Keuntungan ({{ $hasil['target_margin_persen'] }}%)</h6>
                        <div class="fs-5 fw-semibold">+ Rp {{ number_format($hasil['nominal_keuntungan'], 0, ',', '.') }}</div>
                    </div>

                    <div class="text-center p-4 bg-success text-white rounded-4 shadow-sm">
                        <p class="mb-1 text-white-50 fw-semibold">Rekomendasi Harga Jual</p>
                        <h2 class="fw-bold mb-0">Rp {{ number_format($hasil['harga_jual_rekomendasi'], 0, ',', '.') }}</h2>
                    </div>
                </div>
            @else
                <div class="card card-custom p-5 bg-body text-center d-flex flex-column justify-content-center h-100 border text-body-secondary">
                    <div class="display-1 mb-3 opacity-25"><i class="bi bi-clipboard-data"></i></div>
                    <h5>Belum Ada Data</h5>
                    <p class="small">Silakan isi rincian bahan baku dan biaya operasional di sebelah kiri, lalu klik Kalkulasi untuk melihat hasilnya.</p>
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script Dinamis untuk Tambah/Hapus Baris Bahan Baku -->
<script>
    let barisIndex = {{ isset($aiBahan) ? count($aiBahan) : 0 }};
    const btnTambah = document.getElementById('btnTambahBahan');
    const tbodyBahan = document.getElementById('tbodyBahan');

    btnTambah.addEventListener('click', function() {
        barisIndex++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="bahan[${barisIndex}][nama]" class="form-control" placeholder="Contoh: Gula" required></td>
            <td><input type="number" name="bahan[${barisIndex}][harga_beli]" class="form-control" placeholder="0" min="0" required></td>
            <td><input type="number" name="bahan[${barisIndex}][qty_beli]" class="form-control" placeholder="0" min="1" required></td>
            <td><input type="number" name="bahan[${barisIndex}][qty_pakai]" class="form-control" placeholder="0" min="0" required></td>
            <td><button type="button" class="btn btn-danger btn-sm hapus-baris"><i class="bi bi-trash"></i></button></td>
        `;
        tbodyBahan.appendChild(tr);
    });

    // Event Delegation untuk tombol hapus
    tbodyBahan.addEventListener('click', function(e) {
        if(e.target.closest('.hapus-baris')) {
            const tr = e.target.closest('tr');
            if(tbodyBahan.children.length > 1) {
                tr.remove();
            }
        }
    });

    // Dark Mode Script (Diambil dari yang sudah kita buat sebelumnya)
    const btnDarkMode = document.getElementById('btnDarkMode');
    const darkModeIcon = document.getElementById('darkModeIcon');
    const htmlElement = document.documentElement;

    function updateThemeUI(theme) {
        if (theme === 'dark') {
            htmlElement.setAttribute('data-bs-theme', 'dark');
            darkModeIcon.className = 'bi bi-sun-fill text-warning';
        } else {
            htmlElement.setAttribute('data-bs-theme', 'light');
            darkModeIcon.className = 'bi bi-moon-stars-fill';
        }
    }
    const savedTheme = localStorage.getItem('gubah_theme') || 'light';
    updateThemeUI(savedTheme);

    btnDarkMode.addEventListener('click', () => {
        const currentTheme = htmlElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        localStorage.setItem('gubah_theme', newTheme);
        updateThemeUI(newTheme);
    });
</script>
<script>
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(angka);
    }

    function hitungSemua() {
        // 1. Hitung Total Biaya Bahan dari Tabel
        let totalBiayaBahan = 0;
        let barisBahan = document.querySelectorAll('tbody tr');
        
        barisBahan.forEach(function(baris) {
            let hargaBeli = parseFloat(baris.querySelector('.harga-beli').value) || 0;
            let qtyBeli = parseFloat(baris.querySelector('.qty-beli').value) || 1;
            let qtyPakai = parseFloat(baris.querySelector('.qty-pakai').value) || 0;

            let subtotal = (hargaBeli / qtyBeli) * qtyPakai;
            totalBiayaBahan += subtotal;
        });

        // 2. Ambil Biaya Operasional, Margin, dan Jumlah Porsi dari Input
        let inputOperasional = document.querySelector('input[name="biaya_operasional"]');
        let biayaOperasional = inputOperasional ? (parseFloat(inputOperasional.value) || 0) : 5000;

        let inputMargin = document.querySelector('input[name="target_margin"]');
        let targetMargin = inputMargin ? (parseFloat(inputMargin.value) || 40) : 40;

        let inputPorsi = document.getElementById('inputPorsi');
        let jumlahPorsi = inputPorsi ? (parseFloat(inputPorsi.value) || 1) : 1;
        if (jumlahPorsi < 1) jumlahPorsi = 1; // Mencegah pembagian dengan 0

        // 3. Kalkulasi Matematis
        let totalSatuResep = totalBiayaBahan + biayaOperasional;
        let hppPerPcs = totalSatuResep / jumlahPorsi;
        let nominalKeuntunganPerPcs = hppPerPcs * (targetMargin / 100);
        let rekomendasiHargaJualPerPcs = hppPerPcs + nominalKeuntunganPerPcs;

        // 4. RENDER KE PANEL KIRI (Ringkasan Porsi)
        if(document.getElementById('tampilTotalResep')) {
            document.getElementById('tampilTotalResep').innerText = formatRupiah(totalSatuResep);
            document.getElementById('tampilHppPcs').innerText = formatRupiah(hppPerPcs);
            document.getElementById('tampilHargaJualPcs').innerText = formatRupiah(rekomendasiHargaJualPerPcs);
        }

        // 5. RENDER KE PANEL KANAN (Hasil Analisis Keuangan)
        // Menyesuaikan dengan elemen di sebelah kanan layar Anda
        if(document.getElementById('teksTotalBiayaBahan')) {
            document.getElementById('teksTotalBiayaBahan').innerText = formatRupiah(totalBiayaBahan);
        }
        
        // Elemen lain di panel kanan (jika ada ID-nya)
        let elTotalHppKanan = document.getElementById('teksTotalHPP');
        if(elTotalHppKanan) {
            elTotalHppKanan.innerText = formatRupiah(hppPerPcs); // Menampilkan HPP per Pcs agar wajar
        }
    }

    // Jalankan secara Real-Time saat halaman dimuat dan saat ada input yang diubah
    document.addEventListener('DOMContentLoaded', function() {
        hitungSemua(); // Hitung pertama kali

        // Pantau semua elemen input di halaman secara real-time
        document.querySelectorAll('input').forEach(function(input) {
            input.addEventListener('input', hitungSemua);
            input.addEventListener('change', hitungSemua);
        });
    });
</script>
</body>
</html>
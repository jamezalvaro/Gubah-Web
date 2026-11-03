<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GuBah - Asisten Inovasi UMKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Animasi Dot Berjalan (Tracer) */
        .dot-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }
        .dot-step {
            width: 12px;
            height: 12px;
            background-color: #dee2e6; /* Warna abu-abu mati */
            border-radius: 50%;
            transition: all 0.3s ease-in-out;
        }
        .dot-step.active {
            background-color: var(--brand-orange); /* Menyala Oranye */
            transform: scale(1.4);
            box-shadow: 0 0 10px rgba(255, 165, 0, 0.6);
        }
        :root {
            --brand-orange: #FFA500;
            --brand-orange-hover: #e69500;
        }
        .bg-orange { background-color: var(--brand-orange) !important; color: #ffffff !important; }
        .text-orange { color: var(--brand-orange) !important; }
        .btn-orange { background-color: var(--brand-orange); color: #ffffff; border: none; }
        .btn-orange:hover { background-color: var(--brand-orange-hover); color: #ffffff; }
        .card-custom { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        [data-bs-theme="dark"] .card-custom { border: 1px solid #2d3238; }
        .recipe-step-badge {
            width: 28px; height: 28px; background-color: var(--brand-orange); color: white;
            border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 0.85rem;
        }
    </style>
</head>
<body class="bg-body-tertiary">

<nav class="navbar navbar-expand-lg bg-body border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <span class="badge bg-orange rounded-3 p-2 fs-6">🚀 GuBah</span>
            <span class="d-none d-sm-inline fs-5">Innovation Assistant</span>
        </a>
        <button class="btn btn-outline-secondary rounded-pill px-3 d-flex align-items-center gap-2" id="btnDarkMode">
            <i class="bi bi-moon-stars-fill" id="darkModeIcon"></i>
            <span id="darkModeText" class="d-none d-md-inline">Mode Gelap</span>
        </button>
    </div>
</nav>

<div class="py-4 text-center bg-body border-bottom mb-4">
    <div class="container">
        <h2 class="fw-bold text-orange mb-2">Ubah Bahan Dapur Jadi Inovasi Bisnis 💡</h2>
        <p class="text-body-secondary mb-0">Masukkan daftar bahan baku yang Anda miliki, biarkan AI meracik resep bernilai jual tinggi untuk UMKM Anda.</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card card-custom p-4 mb-4 bg-body">
               <form action="{{ route('inventory.generate') }}" method="POST" id="formInovasi">
                    @csrf
                    <div class="mb-3">
                        <label for="bahanBaku" class="form-label fw-bold d-flex align-items-center gap-2">
                            <i class="bi bi-basket-fill text-orange"></i> Daftar Bahan Baku Utama:
                        </label>
                        <!-- Pastikan ID bahanBaku ini ada -->
                        <textarea class="form-control form-control-lg fs-6 rounded-3" id="bahanBaku" name="bahan_baku" rows="3" placeholder="Contoh: Tepung terigu, matcha, mentega... (Pisahkan dengan koma)" required>{{ $bahan_input ?? old('bahan_baku') }}</textarea>
                        <div class="form-text mt-2 text-body-secondary">
                            <i class="bi bi-info-circle"></i> Masukkan <strong>minimal 3 bahan baku</strong> kuliner.
                        </div>
                    </div>
                    
                    <!-- Tombol Submit Normal -->
                    <button type="submit" id="btnSubmit" class="btn btn-orange w-100 fw-bold py-3 rounded-3 shadow-sm fs-6">
                        <i class="bi bi-magic me-1"></i> Generate Ide Inovasi (AI Engine)
                    </button>
                    <!-- Kontainer Animasi Loading (Disembunyikan secara default) -->
                    <div id="loadingState" class="d-none text-center p-4 bg-body-tertiary border rounded-4 shadow-sm mt-3">
                        <!-- Deretan Dot -->
                        <div class="dot-container mb-4" id="dotTracker">
                            <div class="dot-step active"></div>
                            <div class="dot-step"></div>
                            <div class="dot-step"></div>
                            <div class="dot-step"></div>
                            <div class="dot-step"></div>
                        </div>

                        <!-- Teks Dinamis -->
                        <h5 class="fw-bold text-orange mb-2" id="loadingStatusText">Menginisiasi AI Engine...</h5>
                        <p class="text-body-secondary small mb-0">Sistem akan secara otomatis mencari model AI yang paling optimal.<br>Mohon tidak menutup halaman ini.</p>
                    </div>
                </form>
            </div>
            

            @if(isset($resep) && !empty($resep))
                <div class="card card-custom p-4 p-md-5 bg-body border-start border-4 border-warning shadow">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill fw-semibold fs-6 border border-warning-subtle">
                            ✨ Inovasi Resep Rekomendasi AI
                        </span>
                        <span class="text-body-secondary small"><i class="bi bi-clock"></i> Baru saja dibuat</span>
                    </div>

                    <h2 class="fw-bold text-orange mb-3">{{ $resep['nama_produk'] ?? 'Ide Produk Baru' }}</h2>
                    <p class="fs-6 text-body-secondary mb-4 lead">{{ $resep['deskripsi'] ?? '' }}</p>

                    <hr class="my-4">

                    @if(!empty($resep['kebutuhan_bahan']))
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-cart-plus-fill text-orange"></i> Daftar Bahan & Takaran:
                            </h5>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($resep['kebutuhan_bahan'] as $bahan)
                                    <span class="badge bg-body-secondary text-body-emphasis border px-3 py-2 rounded-pill fs-6 fw-normal">
                                        <i class="bi bi-check2 text-success me-1"></i> 
                                        <span class="fw-bold">{{ $bahan['nama_bahan'] ?? 'Bahan' }}</span> 
                                        <span class="text-body-secondary ms-1">({{ $bahan['takaran_praktis'] ?? '-' }})</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(!empty($resep['langkah_pembuatan']))
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-journal-text text-orange"></i> Tahapan Cara Membuat:
                            </h5>
                            <div class="d-flex flex-column gap-3">
                                @foreach($resep['langkah_pembuatan'] as $index => $langkah)
                                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-body-tertiary border">
                                        <span class="recipe-step-badge flex-shrink-0">{{ $index + 1 }}</span>
                                        <div class="pt-1 text-body-emphasis">{{ $langkah }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 p-4 rounded-4 bg-orange text-white text-center shadow-sm">
                        <div class="fs-1 mb-2">💰</div>
                        <h4 class="fw-bold mb-2">Ingin Tahu Hitungan HPP & Target Keuntungannya?</h4>
                        <p class="small text-white-50 mb-3 mx-auto" style="max-width: 500px;">
                            Hitung Harga Pokok Penjualan (HPP) secara presisi menggunakan kalkulator matematika deterministik dan dapatkan strategi promosi produk ini.
                        </p>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#paywallModal" class="btn btn-light text-orange fw-bold px-4 py-2 rounded-pill shadow-sm">
                            🔒 Hitung HPP & Strategi Bisnis (Premium)
                        </button>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
<div class="modal fade" id="paywallModal" tabindex="-1" aria-labelledby="paywallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            
            <!-- Header Modal -->
            <div class="modal-header border-0 bg-warning-subtle text-warning-emphasis rounded-top-4 pb-0">
                <h5 class="modal-title fw-bold" id="paywallModalLabel">
                    <i class="bi bi-lock-fill text-warning me-2"></i>Fitur Premium Terkunci
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Body Modal -->
            <div class="modal-body text-center p-4 p-md-5">
                <div class="display-1 mb-3">💎</div>
                <h3 class="fw-bold mb-3">Waktunya Scale-Up!</h3>
                <p class="text-body-secondary mb-4 fs-6">
                    Akses Kalkulator HPP Deterministik dan Strategi Promosi eksklusif hanya untuk pengguna Premium. Hitung modal dengan presisi, cegah "rugi bandar", dan maksimalkan profit UMKM Anda!
                </p>
                <form action="{{ route('premium.hpp') }}" method="GET">
                    
                    <!-- Looping data bahan yang baru (lengkap dengan nama, takaran, dan gram) -->
                    @if(isset($resep['kebutuhan_bahan']))
                        @foreach($resep['kebutuhan_bahan'] as $index => $bahan)
                            <input type="hidden" name="ai_bahan[{{ $index }}][nama]" value="{{ $bahan['nama_bahan'] ?? '' }}">
                            <input type="hidden" name="ai_bahan[{{ $index }}][takaran]" value="{{ $bahan['takaran_praktis'] ?? '' }}">
                            <input type="hidden" name="ai_bahan[{{ $index }}][gram]" value="{{ $bahan['kebutuhan_gram_ml'] ?? 0 }}">
                        @endforeach
                    @endif

                    <button type="submit" class="btn btn-orange w-100 fw-bold py-3 rounded-pill shadow-sm fs-5 mb-2">
                        <i class="bi bi-calculator me-1"></i> Lanjut ke Kalkulator HPP
                    </button>
                </form>
                <button type="button" class="btn btn-light w-100 fw-semibold py-2 rounded-pill" data-bs-dismiss="modal">
                    Mungkin Nanti Saja
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalKonversiURT" tabindex="-1" aria-labelledby="modalKonversiURTLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-orange text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="modalKonversiURTLabel">
                    <i class="bi bi-scale me-2"></i>Panduan Takaran URT
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-body-secondary mb-3">Gunakan tabel ini sebagai acuan estimasi jika Anda tidak memiliki timbangan digital.</p>
                
                <table class="table table-bordered table-striped text-center align-middle text-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ukuran Rumah Tangga</th>
                            <th>Estimasi (Gram / Ml)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="fw-bold">1 Sendok Teh (sdt)</td><td>± 5 gram / ml</td></tr>
                        <tr><td class="fw-bold">1 Sendok Makan (sdm)</td><td>± 15 gram / ml</td></tr>
                        <tr><td class="fw-bold">1 Gelas Belimbing</td><td>± 200 gram / ml</td></tr>
                        <tr><td class="fw-bold">1 Cup Plastik</td><td>± 240 ml</td></tr>
                        <tr><td class="fw-bold">1 Siung (Bawang)</td><td>± 3 - 5 gram</td></tr>
                        <tr><td class="fw-bold">1 Ikat Kecil (Sayur)</td><td>± 50 gram</td></tr>
                        <tr><td class="fw-bold">1 Ruas Jari (Jahe)</td><td>± 5 - 7 gram</td></tr>
                    </tbody>
                </table>
                <div class="alert alert-warning py-2 mb-0 mt-3 text-center small border-warning-subtle" role="alert">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i> <strong>Tips:</strong> AI GuBah telah mengisikan estimasi gram di tabel HPP Anda untuk mempercepat perhitungan.
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const btnDarkMode = document.getElementById('btnDarkMode');
    const darkModeIcon = document.getElementById('darkModeIcon');
    const darkModeText = document.getElementById('darkModeText');
    const htmlElement = document.documentElement;

    function updateThemeUI(theme) {
        if (theme === 'dark') {
            htmlElement.setAttribute('data-bs-theme', 'dark');
            darkModeIcon.className = 'bi bi-sun-fill text-warning';
            darkModeText.innerText = 'Mode Terang';
        } else {
            htmlElement.setAttribute('data-bs-theme', 'light');
            darkModeIcon.className = 'bi bi-moon-stars-fill';
            darkModeText.innerText = 'Mode Gelap';
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
    document.getElementById('formInovasi').addEventListener('submit', function(e) {
        const textarea = document.getElementById('bahanBaku');
        
        if(textarea.value.trim() !== '') {
            // 1. Kunci dan sembunyikan tombol
            document.getElementById('btnSubmit').classList.add('d-none');
            document.getElementById('loadingState').classList.remove('d-none');
            textarea.setAttribute('readonly', 'true');

            // 2. Logika Animasi Dot Berjalan & Teks Dinamis
            const dots = document.querySelectorAll('.dot-step');
            const statusText = document.getElementById('loadingStatusText');
            
            // Daftar status yang seolah-olah menceritakan proses AI
            const aiStatuses = [
                "Mencari server AI yang tersedia...",
                "Menghubungkan ke Gemini Flash...",
                "Menganalisis kombinasi bahan baku...",
                "Menemukan kecocokan rasa dan tekstur...",
                "Meracik ide resep inovatif...",
                "Memvalidasi struktur data untuk Kalkulator HPP...",
                "Menyelesaikan formulasi resep..."
            ];

            let currentDotIndex = 0;
            let statusIndex = 0;

            // Interval berjalan setiap 400 milidetik
            setInterval(() => {
                // Matikan semua dot
                dots.forEach(d => d.classList.remove('active'));
                
                // Nyalakan dot saat ini
                dots[currentDotIndex % dots.length].classList.add('active');
                
                // Ganti teks setiap kali dot kembali ke awal (setiap 1 siklus)
                if (currentDotIndex > 0 && currentDotIndex % dots.length === 0) {
                    statusIndex++;
                    // Jika teks habis, diam di teks terakhir
                    if(statusIndex < aiStatuses.length) {
                        statusText.innerText = aiStatuses[statusIndex];
                    }
                }
                
                currentDotIndex++;
            }, 400); // Kecepatan perpindahan dot
        }
    });
</script>
</body>
</html>
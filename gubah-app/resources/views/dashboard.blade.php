<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GuBah - Asisten Inovasi UMKM</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .hero-section { background-color: #0d6efd; color: white; padding: 40px 0; text-align: center; border-radius: 0 0 20px 20px; }
        .card-input { margin-top: -30px; border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="hero-section">
    <h1>Halo, UMKM Hebat! 🚀</h1>
    <p>Mari ubah bahan baku di dapurmu menjadi inovasi produk bernilai jual.</p>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card card-input p-4">
                
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('inventory.generate') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="bahanBaku" class="form-label fw-bold">Bahan Baku Apa Saja yang Anda Miliki?</label>
                        <textarea class="form-control" id="bahanBaku" name="bahan_baku" rows="4" placeholder="Contoh: Tepung terigu, matcha, mentega... (Pisahkan dengan tanda koma)" required></textarea>
                        <div class="form-text">Masukkan minimal 3 bahan baku.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">✨ Generate Ide Inovasi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
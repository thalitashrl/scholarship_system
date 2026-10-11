<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Scholarship System</title>
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
            --secondary-gradient: linear-gradient(135deg, #fef08a 0%, #ca8a04 100%);
            --accent-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --dark-bg: #090a0f;
            --navy-blue: #121214;
            --glass-bg: rgba(18, 18, 20, 0.85);
            --glass-border: rgba(234, 179, 8, 0.2);
            --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
            --hover-shadow: 0 20px 40px -15px rgba(234, 179, 8, 0.15);
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: var(--dark-bg);
            color: #cbd5e1;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* Navbar Styling */
        .navbar-landing {
            background: rgba(9, 10, 15, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(234, 179, 8, 0.15);
            transition: all 0.3s ease;
        }

        .navbar-brand-custom {
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }

        .nav-link-custom {
            color: #cbd5e1;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.5rem 1.25rem !important;
            transition: color 0.2s ease;
        }

        .nav-link-custom:hover {
            color: #eab308;
        }

        /* Hero Section */
        .hero-section {
            padding: 10rem 0 6rem 0;
            position: relative;
            background: radial-gradient(circle at 10% 20%, rgba(234, 179, 8, 0.04) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(202, 138, 4, 0.04) 0%, transparent 40%);
            overflow: hidden;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            color: #ffffff;
            margin-bottom: 1.5rem;
        }

        .hero-title span {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 2.5rem;
        }

        /* Animated Floating Circles */
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.3;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: #eab308;
            top: -50px;
            left: -50px;
            animation: floatShape 8s infinite alternate;
        }

        .shape-2 {
            width: 400px;
            height: 400px;
            background: #ca8a04;
            bottom: -100px;
            right: -100px;
            animation: floatShape 12s infinite alternate-reverse;
        }

        @keyframes floatShape {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(40px, 40px) scale(1.1); }
        }

        /* Glassmorphic Cards & Badges */
        .glass-badge {
            background: rgba(234, 179, 8, 0.08);
            border: 1px solid rgba(234, 179, 8, 0.2);
            color: #eab308;
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .btn-primary-custom {
            background: var(--primary-gradient);
            color: #090a0f;
            font-weight: 700;
            padding: 0.85rem 2rem;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 15px rgba(234, 179, 8, 0.3);
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(234, 179, 8, 0.4);
            color: #090a0f;
            background: linear-gradient(135deg, #facc15 0%, #ca8a04 100%);
        }

        .btn-secondary-custom {
            background: transparent;
            color: #eab308;
            font-weight: 700;
            padding: 0.85rem 2rem;
            border-radius: 12px;
            border: 1px solid rgba(234, 179, 8, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn-secondary-custom:hover {
            background: rgba(234, 179, 8, 0.05);
            border-color: #eab308;
            color: #eab308;
            transform: translateY(-2px);
        }

        /* Features Section */
        .section-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: #ffffff;
            position: relative;
            margin-bottom: 3.5rem;
            text-align: center;
        }

        .section-title::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 4px;
            background: var(--primary-gradient);
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 10px;
        }

        .feature-card {
            background: #18181b;
            border: 1px solid #27272a;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--hover-shadow);
            border-color: rgba(234, 179, 8, 0.3);
        }

        .feature-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #090a0f;
            margin-bottom: 1.5rem;
            background: var(--primary-gradient);
        }

        .feature-icon-blue {
            background: linear-gradient(135deg, #facc15 0%, #eab308 100%);
        }

        .feature-icon-red {
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
        }

        /* Stats Section */
        .stats-section {
            background: #121214;
            color: white;
            padding: 5rem 0;
            position: relative;
            z-index: 1;
            border-top: 1px solid rgba(234, 179, 8, 0.1);
            border-bottom: 1px solid rgba(234, 179, 8, 0.1);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #94a3b8;
            font-weight: 600;
            font-size: 1rem;
        }

        /* Scholarship Cards */
        .scholarship-card {
            background: #18181b;
            border: 1px solid #27272a;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .scholarship-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--hover-shadow);
            border-color: rgba(234, 179, 8, 0.3);
        }

        .card-tag {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .tag-internal {
            background: rgba(234, 179, 8, 0.08);
            color: #eab308;
        }

        .tag-external {
            background: rgba(251, 191, 36, 0.08);
            color: #fbbf24;
        }

        /* Demo Accounts Table Section */
        .demo-accounts-section {
            background: #18181b;
            border-radius: 24px;
            border: 1px solid #27272a;
            padding: 3rem;
            box-shadow: var(--card-shadow);
        }

        .table-custom {
            margin-top: 1.5rem;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #27272a;
        }

        .table-custom th {
            background-color: #27272a;
            color: #f8fafc;
            font-weight: 700;
            padding: 1rem;
            border-bottom: 1px solid #27272a;
        }

        .table-custom td {
            padding: 1.2rem 1rem;
            vertical-align: middle;
            color: #cbd5e1;
            border-bottom: 1px solid #27272a;
        }

        .badge-role {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .badge-admin { background-color: rgba(239, 68, 68, 0.15); color: #ef4444; }
        .badge-staff { background-color: rgba(245, 158, 11, 0.15); color: #f59e0b; }
        .badge-wd3 { background-color: rgba(16, 185, 129, 0.15); color: #10b981; }
        .badge-mahasiswa { background-color: rgba(59, 130, 246, 0.15); color: #3b82f6; }

        /* Footer */
        footer {
            background: #090a0f;
            color: #94a3b8;
            padding: 4rem 0 2rem 0;
            border-top: 1px solid rgba(234, 179, 8, 0.15);
        }

        footer h6 {
            color: white;
            font-weight: 700;
            margin-bottom: 1.25rem;
        }

        footer a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        footer a:hover {
            color: #eab308;
        }

        /* Font Readability Overrides */
        .text-muted, p.text-muted, .feature-card p, .scholarship-card p, .hero-subtitle {
            color: #cbd5e1 !important; /* Lighter slate color for absolute readability on dark backgrounds */
        }
        .small.text-muted, span.text-muted, .text-muted.small {
            color: #a1a1aa !important; /* Clear light grey for secondary info */
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-landing fixed-top">
        <div class="container">
            <a class="navbar-brand navbar-brand-custom" href="#">
                <i class="bi bi-mortarboard-fill me-2 text-primary"></i>
                Scholarship System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#fitur">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#beasiswa">Beasiswa</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a class="btn btn-primary-custom d-flex align-items-center" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Portal Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <header class="hero-section">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <span class="glass-badge">
                        <i class="bi bi-sparkles me-2 text-primary"></i> Sistem Informasi Beasiswa & Kemahasiswaan
                    </span>
                    <h1 class="hero-title">Raih Pendidikan Terbaik dengan <span>Beasiswa Impian</span></h1>
                    <p class="hero-subtitle">
                        Temukan berbagai kesempatan beasiswa internal maupun eksternal, ajukan surat permohonan kemahasiswaan secara cepat, dan konsultasikan rencana studi Anda langsung dengan jajaran Wakil Dekan & Staf Kemahasiswaan.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#beasiswa" class="btn btn-primary-custom">
                            Jelajahi Beasiswa <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative">
                        <!-- Graphic/Image placeholder replaced with a sleek glassmorphic card interface -->
                        <div class="card border-0 shadow-lg p-4 rounded-4" style="background: rgba(24, 24, 27, 0.95) !important; backdrop-filter: blur(20px); transform: perspective(1000px) rotateY(-10deg) rotateX(10deg); border: 1px solid rgba(234, 179, 8, 0.2) !important;">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle p-2 text-warning me-3" style="background: rgba(234, 179, 8, 0.15) !important;">
                                        <i class="bi bi-journal-check fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="m-0 font-weight-bold text-white">Pengajuan Beasiswa Aktif</h6>
                                        <span class="text-muted small">Update Real-time</span>
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success border-0 px-3 py-2 rounded-3">Online</span>
                            </div>
                            
                            <div class="mb-3 p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: rgba(255, 255, 255, 0.05);">
                                <div>
                                    <h6 class="m-0 text-white" style="font-size:0.95rem">Beasiswa Bank Indonesia</h6>
                                    <span class="text-muted small"><i class="bi bi-clock me-1"></i> Sisa 12 Hari</span>
                                </div>
                                <span class="badge bg-warning text-dark px-2 py-1 rounded">Eksternal</span>
                            </div>
                            <div class="mb-3 p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: rgba(255, 255, 255, 0.05);">
                                <div>
                                    <h6 class="m-0 text-white" style="font-size:0.95rem">Beasiswa PPA Internal</h6>
                                    <span class="text-muted small"><i class="bi bi-clock me-1"></i> Sisa 30 Hari</span>
                                </div>
                                <span class="badge bg-warning text-dark px-2 py-1 rounded">Internal</span>
                            </div>
                            
                            <div class="d-flex gap-3 justify-content-between mt-4">
                                <div class="text-center flex-fill border-end py-2" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                                    <h4 class="m-0 font-weight-bold" style="color:#eab308">{{ $stats['totalBeasiswa'] }}</h4>
                                    <span class="text-muted small">Total Program</span>
                                </div>
                                <div class="text-center flex-fill border-end py-2" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                                    <h4 class="m-0 font-weight-bold" style="color:#eab308">{{ $stats['totalSurat'] }}</h4>
                                    <span class="text-muted small">Surat Diproses</span>
                                </div>
                                <div class="text-center flex-fill py-2">
                                    <h4 class="m-0 font-weight-bold" style="color:#eab308">{{ $stats['totalBimbingan'] }}</h4>
                                    <span class="text-muted small">Konsultasi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- STATS COUNTER -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-6 col-md-3">
                    <div class="stat-number">{{ $stats['totalBeasiswa'] }}</div>
                    <div class="stat-label">Program Beasiswa</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-number">{{ $stats['totalMahasiswa'] }}</div>
                    <div class="stat-label">Mahasiswa Terdaftar</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-number">{{ $stats['totalSurat'] }}</div>
                    <div class="stat-label">Layanan Surat Selesai</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-number">{{ $stats['totalBimbingan'] }}</div>
                    <div class="stat-label">Sesi Bimbingan Akademik</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="py-6 my-5" id="fitur">
        <div class="container">
            <h2 class="section-title">Layanan & Fitur Unggulan</h2>
            <div class="row g-4">
                <!-- Beasiswa -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-award"></i>
                        </div>
                        <h5 class="font-weight-bold mb-3">Manajemen Beasiswa</h5>
                        <p class="text-muted small mb-0">
                            Cari dan daftarkan diri Anda pada program beasiswa yang sesuai dengan kualifikasi prestasi akademik maupun non-akademik Anda.
                        </p>
                    </div>
                </div>
                <!-- Surat -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper feature-icon-blue">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h5 class="font-weight-bold mb-3">Layanan Administrasi</h5>
                        <p class="text-muted small mb-0">
                            Ajukan surat pengantar, rekomendasi beasiswa, dan surat keterangan kemahasiswaan lainnya secara digital dan pantau statusnya secara real-time.
                        </p>
                    </div>
                </div>
                <!-- Konsultasi -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper feature-icon-red">
                            <i class="bi bi-chat-quote"></i>
                        </div>
                        <h5 class="font-weight-bold mb-3">Konsultasi Bimbingan</h5>
                        <p class="text-muted small mb-0">
                            Reservasi jadwal pertemuan konsultasi studi atau bimbingan khusus kemahasiswaan langsung dengan dosen maupun Wakil Dekan III.
                        </p>
                    </div>
                </div>
                <!-- Live Chat -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper" style="background:linear-gradient(135deg, #10b981 0%, #059669 100%)">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <h5 class="font-weight-bold mb-3">Real-time Live Chat</h5>
                        <p class="text-muted small mb-0">
                            Interaksi langsung secara real-time antara mahasiswa dan staf kemahasiswaan untuk mengatasi keluhan administrasi dengan cepat.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SCHOLARSHIPS SECTION -->
    <section class="py-6" id="beasiswa" style="background-color: #121214 !important; border-top: 1px solid rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.03);">
        <div class="container">
            <h2 class="section-title">Program Beasiswa Aktif</h2>
            <div class="row g-4">
                @forelse($beasiswa as $item)
                    <div class="col-md-6 col-lg-4">
                        <div class="scholarship-card p-4">
                            <div>
                                <span class="card-tag {{ $item->jenis === 'internal' ? 'tag-internal' : 'tag-external' }}">
                                    Beasiswa {{ ucfirst($item->jenis) }}
                                </span>
                                <h5 class="font-weight-bold mb-3 text-white">{{ $item->judul }}</h5>
                                <p class="text-muted small text-truncate-3 mb-4" style="height: 4.5rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                    {{ $item->deskripsi }}
                                </p>
                            </div>
                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    <i class="bi bi-people me-1"></i> Kuota: <strong>{{ $item->kuota }}</strong>
                                </span>
                                <span class="text-danger small font-weight-bold">
                                    <i class="bi bi-calendar-event me-1"></i> s.d. {{ $item->deadline->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="text-muted mb-3"><i class="bi bi-inbox fs-1"></i></div>
                        <h6 class="text-muted">Tidak ada program beasiswa aktif saat ini.</h6>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <h6 class="font-weight-bold mb-3"><i class="bi bi-mortarboard-fill me-2"></i>Scholarship System</h6>
                    <p class="small text-muted" style="max-width: 350px;">
                        Portal administrasi kemahasiswaan dan beasiswa akademik yang terintegrasi, transparan, dan real-time.
                    </p>
                </div>
                <div class="col-md-3">
                    <h6>Navigasi</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#fitur">Fitur Utama</a></li>
                        <li class="mb-2"><a href="#beasiswa">Beasiswa Aktif</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Tautan Portal</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('login') }}">Masuk Portal Web</a></li>
                        <li class="mb-2"><a href="/api/v1/beasiswa" target="_blank">Endpoint API Beasiswa</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-top border-secondary pt-4 text-center">
                <p class="small text-muted mb-0">
                    &copy; 2026 Scholarship System. Semua hak cipta dilindungi.
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

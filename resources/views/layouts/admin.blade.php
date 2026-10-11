<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Scholarship System</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-gradient: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
            --navy-blue: #090a0f;
            --light-bg: #121214;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -2px rgba(0, 0, 0, 0.3);
            --premium-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -4px rgba(0, 0, 0, 0.4);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-bg);
            color: #cbd5e1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Override Card, Form, Table, Modal untuk Tema Gelap Premium */
        .card, .card-premium {
            background-color: #18181b !important;
            border: 1px solid #27272a !important;
            color: #ffffff !important;
        }

        .text-dark {
            color: #ffffff !important;
        }

        .text-muted {
            color: #94a3b8 !important;
        }

        .border {
            border-color: #27272a !important;
        }

        .table {
            color: #cbd5e1 !important;
        }

        .table th, .table td {
            border-color: #27272a !important;
            background-color: transparent !important;
        }

        .table thead th {
            color: #f8fafc !important;
            background-color: #27272a !important;
        }

        .modal {
            z-index: 1060 !important;
        }

        .modal-backdrop {
            z-index: 1050 !important;
        }

        .modal-content {
            background-color: #18181b !important;
            border: 1px solid #27272a !important;
            color: #ffffff !important;
        }

        .modal-header, .modal-footer {
            border-color: #27272a !important;
        }

        .form-control, .form-select {
            background-color: #121214 !important;
            border: 1px solid #27272a !important;
            color: #ffffff !important;
        }

        .form-control:focus, .form-select:focus {
            background-color: #121214 !important;
            color: #ffffff !important;
            border-color: #eab308 !important;
            box-shadow: 0 0 0 0.25rem rgba(234, 179, 8, 0.15) !important;
        }

        .alert-dismissible .btn-close {
            filter: invert(1);
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--navy-blue);
            color: #ffffff;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.15);
            border-right: 1px solid rgba(234, 179, 8, 0.1);
        }

        .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.25rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-menu {
            padding: 1rem 0;
            list-style: none;
            margin: 0;
        }

        .sidebar-menu-item {
            padding: 0.25rem 1rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .sidebar-link i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
            transition: transform 0.2s ease;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .sidebar-link:hover i {
            transform: translateX(3px);
        }

        .sidebar-link.active {
            color: #090a0f;
            background: var(--primary-gradient);
            box-shadow: 0 4px 12px rgba(234, 179, 8, 0.35);
        }

        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Navbar Styles */
        .navbar-custom {
            background-color: #18181b;
            border-bottom: 1px solid #27272a;
            padding: 0.75rem 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .navbar-custom h5 {
            color: #ffffff;
        }

        .content-area {
            padding: 2rem;
            flex: 1;
        }

        /* Premium UI Components Override */
        .card-premium {
            background: #18181b !important;
            border: 1px solid #27272a !important;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-premium:hover {
            transform: translateY(-2px);
            box-shadow: var(--premium-shadow);
        }

        .card-stat {
            position: relative;
            overflow: hidden;
        }

        .card-stat::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: rgba(234, 179, 8, 0.03) !important;
            border-radius: 50%;
            top: -50px;
            right: -50px;
        }

        .badge-premium {
            padding: 0.4em 0.8em;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* Custom Button & Subtle Color Palette Overrides */
        .btn-primary {
            background: var(--primary-gradient) !important;
            border: none !important;
            color: #090a0f !important;
            font-weight: 700 !important;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #facc15 0%, #ca8a04 100%) !important;
            color: #090a0f !important;
            box-shadow: 0 4px 12px rgba(234, 179, 8, 0.25) !important;
        }
        .btn-outline-primary {
            border-color: #eab308 !important;
            color: #eab308 !important;
            font-weight: 600 !important;
        }
        .btn-outline-primary:hover {
            background: var(--primary-gradient) !important;
            color: #090a0f !important;
            border-color: #ca8a04 !important;
        }
        .btn-light {
            background-color: #121214 !important;
            border: 1px solid #27272a !important;
            color: #ffffff !important;
        }
        .btn-light:hover {
            background-color: #27272a !important;
            border-color: #eab308 !important;
            color: #eab308 !important;
        }

        .bg-primary-subtle {
            background-color: rgba(234, 179, 8, 0.15) !important;
            color: #eab308 !important;
        }
        .text-primary, .text-navy-blue {
            color: #eab308 !important;
        }
        .bg-success-subtle {
            background-color: rgba(16, 185, 129, 0.15) !important;
            color: #10b981 !important;
        }
        .bg-warning-subtle {
            background-color: rgba(245, 158, 11, 0.15) !important;
            color: #f59e0b !important;
        }
        .bg-info-subtle {
            background-color: rgba(6, 182, 212, 0.15) !important;
            color: #06b6d4 !important;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
            .sidebar-toggle-btn {
                display: block !important;
            }
        }

        .sidebar-toggle-btn {
            display: none;
        }

        /* Font Contrast & Readability Overrides */
        .text-muted, .table .text-muted, td .text-muted, span.text-muted, .text-muted.small {
            color: #a1a1aa !important; /* Lighter grey for visible muted texts */
        }
        .text-dark, td .text-dark, .table .text-dark, .fw-semibold.text-dark {
            color: #ffffff !important; /* Force pure white for bold items */
        }
        .small, .small strong, td .small, td .small strong, .text-wrap {
            color: #cbd5e1 !important; /* Lighter grey for subtext readability */
        }
        .table td, .table th, td, th {
            color: #cbd5e1 !important; /* Force readable light grey text for table contents */
        }
        .text-navy-blue {
            color: #ffffff !important; /* Main title text */
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand d-flex align-items-center justify-content-between">
            <span><i class="bi bi-mortarboard-fill me-2"></i>Scholarship</span>
            <button class="btn btn-link text-white sidebar-toggle-btn d-lg-none p-0" onclick="toggleSidebar()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <ul class="sidebar-menu">
            @if(Auth::user()->hasRole('mahasiswa'))
                <li class="sidebar-menu-item">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="sidebar-link {{ Request::routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('mahasiswa.surat.index') }}" class="sidebar-link {{ Request::routeIs('mahasiswa.surat.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>Pengajuan Surat</span>
                    </a>
                </li>
            @else
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.beasiswa.index') }}" class="sidebar-link {{ Request::routeIs('admin.beasiswa.*') ? 'active' : '' }}">
                        <i class="bi bi-award-fill"></i>
                        <span>Kelola Beasiswa</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.surat.index') }}" class="sidebar-link {{ Request::routeIs('admin.surat.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>Pengajuan Surat</span>
                    </a>
                </li>
            @endif

            <!-- Konsultasi (Semua Role) -->
            <li class="sidebar-menu-item">
                <a href="{{ route('consultation.index') }}" class="sidebar-link {{ Request::routeIs('consultation.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar2-check-fill"></i>
                    <span>Konsultasi</span>
                </a>
            </li>

            <!-- Live Chat (Semua Role) -->
            <li class="sidebar-menu-item">
                <a href="{{ route('chat.index') }}" class="sidebar-link {{ Request::routeIs('chat.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-left-text-fill"></i>
                    <span>Live Chat</span>
                </a>
            </li>

            <!-- Master Data (Hanya Admin) -->
            @if(Auth::user()->hasRole('admin'))
                <li class="sidebar-menu-header px-4 pt-3 pb-1 text-uppercase font-weight-bold text-muted" style="font-size: 0.75rem; letter-spacing: 0.05em; list-style: none;">Master Data</li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ Request::routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Kelola Pengguna</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.jenis-surat.index') }}" class="sidebar-link {{ Request::routeIs('admin.jenis-surat.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-medical-fill"></i>
                        <span>Jenis Surat</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.jenis-beasiswa.index') }}" class="sidebar-link {{ Request::routeIs('admin.jenis-beasiswa.*') ? 'active' : '' }}">
                        <i class="bi bi-tags-fill"></i>
                        <span>Jenis Beasiswa</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper" id="mainWrapper">
        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg navbar-custom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary me-3 sidebar-toggle-btn d-lg-none" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="m-0 font-weight-bold text-navy">{{ Auth::user()->hasRole('mahasiswa') ? 'Portal Mahasiswa' : 'Kemahasiswaan Panel' }}</h5>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center border" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="bg-indigo text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; background: var(--primary-gradient);">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span>{{ Auth::user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userMenu">
                    <li class="dropdown-header">
                        <strong>{{ Auth::user()->name }}</strong><br>
                        <span class="text-muted small">{{ Auth::user()->email }}</span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger d-flex align-items-center">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- CONTENT -->
        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show card-premium p-3 border-0 bg-success-subtle text-success-emphasis mb-4 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show card-premium p-3 border-0 bg-danger-subtle text-danger-emphasis mb-4 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show card-premium p-3 border-0 bg-danger-subtle text-danger-emphasis mb-4 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Terjadi Kesalahan Validasi:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    @yield('modals')

    <!-- Bootstrap Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
    </script>
    @yield('scripts')
</body>
</html>

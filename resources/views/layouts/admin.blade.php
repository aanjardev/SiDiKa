<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Admin Panel') | SiDiKa</title>

    {{-- @vite([
          'resources/admin_theme/css/core/libs.min.css',
          'resources/admin_theme/vendor/aos/dist/aos.css',
          'resources/admin_theme/css/hope-ui.min.css',
          'resources/admin_theme/css/custom.min.css',
          'resources/admin_theme/css/dark.min.css',
          'resources/admin_theme/css/customizer.min.css',
          'resources/admin_theme/css/rtl.min.css'
      ]) --}}


    <link rel="shortcut icon" href="{{ asset('mainIMG/logoDK.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Admin CSS -->
    <link href="{{ asset('css/adminsidebar.css') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/8794378048.js" crossorigin="anonymous"></script>

    @stack('styles')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  </head>
  <body class="  ">
    <!-- loader Start -->

    <div id="loading">
        <div class="loader"></div>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <img src="{{ asset('mainIMG/logoDinoyo.png') }}" alt="Logo" class="sidebar-logo">
            <div class="sidebar-brand">
                <h4 class="sidebar-brand-text">Dinoyo Kamera</h4>
                <span class="sidebar-brand-subtitle">Admin Panel</span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <div class="sidebar-menu">
            <!-- Home Section -->
            <div class="menu-section">
                <h6 class="menu-section-title">Home</h6>
                <div class="menu-item">
                    <a href="{{ route('admin.dashboard') }}" class="menu-link active">
                        <i class="fas fa-th-large menu-icon"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>

            <!-- Master Data Section -->
            <div class="menu-section">
                <h6 class="menu-section-title">Master Data</h6>

                <div class="menu-item">
                    <a href="{{ route('admin.products.index') }}" class="menu-link">
                        <i class="fas fa-box menu-icon"></i>
                        <span>Data Produk</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a href="{{ route('admin.customers.index') }}" class="menu-link">
                        <i class="fas fa-users menu-icon"></i>
                        <span>Data Pelanggan</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a href="{{ route('admin.employees.index') }}" class="menu-link">
                        <i class="fas fa-user-tie menu-icon"></i>
                        <span>Data Karyawan</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a href="{{ route('admin.categories.index') }}" class="menu-link">
                        <i class="fas fa-list menu-icon"></i>
                        <span>Daftar Kategori</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a href="{{ route('admin.branches.index') }}" class="menu-link">
                        <i class="fas fa-store menu-icon"></i>
                        <span>Data Cabang</span>
                    </a>
                </div>
            </div>

            <!-- Transaksi Section -->
            <div class="menu-section">
                <h6 class="menu-section-title">Transaksi</h6>

                <div class="menu-item">
                    <a href="{{ route('admin.sales') }}" class="menu-link">
                        <i class="fas fa-shopping-cart menu-icon"></i>
                        <span>Penjualan</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a href="{{ route('admin.purchases') }}" class="menu-link">
                        <i class="fas fa-shopping-bag menu-icon"></i>
                        <span>Pembelian</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a href="{{ route('admin.quality-control') }}" class="menu-link">
                        <i class="fas fa-check-circle menu-icon"></i>
                        <span>Quality Control</span>
                    </a>
                </div>
            </div>

            <!-- Manajemen Section -->
            <div class="menu-section">
                <h6 class="menu-section-title">Manajemen</h6>

                <div class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="fas fa-cog menu-icon"></i>
                        <span>Setting Web Katalog</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="fas fa-bullhorn menu-icon"></i>
                        <span>Manajemen Promosi</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="fas fa-user-shield menu-icon"></i>
                        <span>Manajemen Hak Akses</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar Footer - User Profile -->
        <div class="sidebar-footer">
            <div class="user-profile" data-bs-toggle="dropdown">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-info">
                    <p class="user-name">Syaiful Budiyanto</p>
                    <p class="user-role">Manager</p>
                </div>
                <i class="fas fa-chevron-right" style="color: var(--text-muted); font-size: 0.8rem;"></i>
            </div>

            <!-- Dropdown Menu -->
            <ul class="dropdown-menu dropdown-menu-end" style="width: calc(100% - 2rem); margin: 0.5rem 1rem;">
                <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Mobile Toggle Button -->
    <button class="sidebar-toggle-mobile" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content pt-3 mb-0 mt-0 d-flex flex-column pb-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="page-title mb-0">@yield('title', 'Dashboard')</h1>
                <div class="d-flex align-items-center gap-3">
                    @stack('page-actions')
                </div>
            </div>


        <!-- Content -->
        <div class="content-wrapper pt-0 pb-3">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="mt-auto p-2 border-top">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-muted mb-0">
                        &copy; <script>document.write(new Date().getFullYear())</script>
                        <strong>Dinoyo Kamera</strong> - Sistem Informasi Dinoyo Kamera
                    </p>
                </div>
            </div>
        </footer>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // Hide loader when page is loaded
        window.addEventListener('load', function() {
            document.getElementById('loading').style.display = 'none';
        });

        // Mobile sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 991) {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Set active menu based on current URL
        document.addEventListener('DOMContentLoaded', function() {
            const currentUrl = window.location.href;
            const menuLinks = document.querySelectorAll('.menu-link, .submenu-link');

            menuLinks.forEach(link => {
                if (link.href === currentUrl) {
                    link.classList.add('active');

                    // If it's a submenu item, also expand the parent menu
                    const parentCollapse = link.closest('.collapse');
                    if (parentCollapse) {
                        parentCollapse.classList.add('show');
                        const parentLink = document.querySelector('[data-bs-target="#' + parentCollapse.id + '"]');
                        if (parentLink) {
                            parentLink.classList.remove('collapsed');
                        }
                    }
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>

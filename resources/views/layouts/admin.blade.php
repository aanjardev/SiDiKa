@php
$setting = \App\Models\CatalogSettings::first();
@endphp
<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Admin Panel') | SiDiKa</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

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
    <link href="{{ asset('css/adminpage.css') }}" rel="stylesheet">
    <!-- Form Validation CSS -->
    <link href="{{ asset('css/form-validation.css') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/8794378048.js" crossorigin="anonymous"></script>

    @stack('styles')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/alert.js') }}"></script>


<body class="  ">
    @include('partials.alerts')
    <!-- loader Start -->

    <div id="loading">
        <div class="loader"></div>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <img src="{{ $setting->logo_url }}"
                alt="{{ $setting->nama_website }} Logo"
                class="sidebar-logo">
            <div class="sidebar-brand">
                <h4 class="sidebar-brand-text">{{ $setting->nama_website}}</h4>
                <span class="sidebar-brand-subtitle">Admin Panel</span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <div class="sidebar-menu" id="sidebarMenu">
            <!-- Dashboard (direct link) -->
            <div class="menu-section">
                <a href="{{ route('admin.dashboard') }}" class="menu-link d-flex align-items-center">
                    <i class="fas fa-th-large menu-icon"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- Master Data Section -->
            <div class="menu-section">
                <button class="menu-link menu-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#masterDataMenu" aria-expanded="false" aria-controls="masterDataMenu">
                    <i class="fas fa-database menu-icon"></i>
                    <span>Master Data</span>
                    <i class="fas fa-chevron-right menu-arrow"></i>
                </button>

                <div class="collapse submenu" id="masterDataMenu">
                    <div class="submenu-item">
                        <a href="{{ route('admin.products.index') }}" class="submenu-link">
                            <i class="fas fa-box submenu-icon"></i>
                            <span>Data Produk</span>
                        </a>
                    </div>

                    <div class="submenu-item">
                        <a href="{{ route('admin.customers.index') }}" class="submenu-link">
                            <i class="fas fa-users submenu-icon"></i>
                            <span>Data Pelanggan</span>
                        </a>
                    </div>

                    @if (Auth::user()->role == 'manager')
                    <div class="submenu-item">
                        <a href="{{ route('admin.employees.index') }}" class="submenu-link">
                            <i class="fas fa-user-tie submenu-icon"></i>
                            <span>Data Karyawan</span>
                        </a>
                    </div>
                    @endif

                    <div class="submenu-item">
                        <a href="{{ route('admin.categories.index') }}" class="submenu-link">
                            <i class="fas fa-list submenu-icon"></i>
                            <span>Daftar Kategori</span>
                        </a>
                    </div>

                    <div class="submenu-item">
                        <a href="{{ route('admin.branches.index') }}" class="submenu-link">
                            <i class="fas fa-store submenu-icon"></i>
                            <span>Data Cabang</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Transaksi Section -->
            <div class="menu-section">
                <button class="menu-link menu-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#transaksiMenu" aria-expanded="false" aria-controls="transaksiMenu">
                    <i class="fas fa-exchange-alt menu-icon"></i>
                    <span>Operasional</span>
                    <i class="fas fa-chevron-right menu-arrow"></i>
                </button>

                <div class="collapse submenu" id="transaksiMenu">
                    <div class="submenu-item">
                        <a href="{{ route('admin.sales.index') }}" class="submenu-link">
                            <i class="fas fa-shopping-cart submenu-icon"></i>
                            <span>Penjualan</span>
                        </a>
                    </div>

                    <div class="submenu-item">
                        <a href="{{ route('admin.purchases.index') }}" class="submenu-link">
                            <i class="fas fa-shopping-bag submenu-icon"></i>
                            <span>Pembelian</span>
                        </a>
                    </div>

                    <div class="submenu-item">
                        <a href="{{ route('admin.quality-control.index') }}" class="submenu-link">
                            <i class="fas fa-check-circle submenu-icon"></i>
                            <span>Quality Control</span>
                        </a>
                    </div>

                    <div class="submenu-item">
                        <a href="{{ route('admin.products.photos') }}" class="submenu-link">
                            <i class="fas fa-image submenu-icon"></i>
                            <span>Foto Produk</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Manajemen Section -->
            <div class="menu-section">
                <button class="menu-link menu-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#manajemenMenu" aria-expanded="false" aria-controls="manajemenMenu">
                    <i class="fas fa-briefcase menu-icon"></i>
                    <span>Manajemen</span>
                    <i class="fas fa-chevron-right menu-arrow"></i>
                </button>

                <div class="collapse submenu" id="manajemenMenu">
                    <div class="submenu-item">
                        <a href="{{ route('admin.catalog-settings.index') }}" class="submenu-link">
                            <i class="fas fa-cog submenu-icon"></i>
                            <span>Setting Web Katalog</span>
                        </a>
                    </div>

                    {{-- Manajemen Promosi removed per request --}}

                    @if (Auth::user()->role == 'manager')
                    <div class="submenu-item">
                        <a href="{{ route('admin.permissions') }}" class="submenu-link">
                            <i class="fas fa-user-shield submenu-icon"></i>
                            <span>Manajemen Hak Akses</span>
                        </a>
                    </div>
                    @endif
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
                    <p class="user-name">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="user-role">{{ isset(Auth::user()->role) ? ucfirst(Auth::user()->role) : '' }}</p>
                </div>
                <i class="fas fa-chevron-right" style="color: var(--text-muted); font-size: 0.8rem;"></i>
            </div>

            <!-- Dropdown Menu -->
            <ul class="dropdown-menu dropdown-menu-end" style="width: calc(100% - 2rem); margin: 0.5rem 1rem;">
                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
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
    <button class="sidebar-toggle-mobile" id="sidebarToggle" aria-label="Toggle Sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content pt-5 mb-0 mt-0 d-flex flex-column pb-0">
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
                        &copy; <script>
                            document.write(new Date().getFullYear())
                        </script>
                        <strong>Dinoyo Kamera</strong> - Sistem Informasi Dinoyo Kamera
                    </p>
                </div>
            </div>
        </footer>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Clickable rows script -->
    <script src="{{ asset('js/clickable-rows.js') }}"></script>
    <!-- Form Validation JS -->
    <script src="{{ asset('js/form-validation.js') }}"></script>

    <script>
        // Hide loader when page is loaded
        window.addEventListener('load', function() {
            document.getElementById('loading').style.display = 'none';
        });

        // Mobile sidebar toggle with overlay
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
            // Toggle icon
            const icon = sidebarToggle.querySelector('i');
            if (sidebar.classList.contains('show')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
                document.body.style.overflow = 'hidden';
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
                document.body.style.overflow = '';
            }
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
            // Reset icon
            const icon = sidebarToggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleSidebar();
            });
        }

        // Close sidebar when clicking overlay
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                closeSidebar();
            });
        }

        // Close sidebar when clicking outside on mobile/tablet
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 991) {
                if (sidebar.classList.contains('show') &&
                    !sidebar.contains(event.target) &&
                    !sidebarToggle.contains(event.target)) {
                    closeSidebar();
                }
            }
        });

        // Close sidebar on window resize if switching to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 991) {
                closeSidebar();
            }
        });

        // Set active menu based on current URL
        document.addEventListener('DOMContentLoaded', function() {
            // Close sidebar when clicking on menu links (mobile)
            if (window.innerWidth <= 991) {
                const menuLinks = document.querySelectorAll('.submenu-link, .menu-link[href]');
                menuLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        setTimeout(() => {
                            closeSidebar();
                        }, 300);
                    });
                });
            }
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.submenu-link, .menu-link[href]');

            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (!href) {
                    return;
                }

                let linkPath;
                try {
                    // Get the pathname from href (handle both relative and absolute URLs)
                    const linkUrl = new URL(href, window.location.origin);
                    linkPath = linkUrl.pathname;
                } catch (e) {
                    // Fallback: if URL parsing fails, use href as-is (shouldn't happen with Laravel routes)
                    linkPath = href.split(/[?#]/)[0];
                }

                // Check for exact match
                if (linkPath === currentPath) {
                    link.classList.add('active');
                    activateParentMenu(link);
                }
                // Check if current path is a child route of this menu link
                // e.g., /admin/purchases/create is a child of /admin/purchases
                // Exception: /admin/products/photos should NOT be considered a child of /admin/products
                else if (currentPath.startsWith(linkPath + '/') && linkPath !== currentPath && linkPath !== '/') {
                    // Exclude /admin/products/photos and its child routes from being treated as child of /admin/products
                    // This ensures "Foto Produk" menu stays separate from "Produk" menu
                    if (linkPath === '/admin/products' && currentPath.startsWith('/admin/products/photos')) {
                        // Skip - Foto Produk is a separate menu under Operasional, not under Produk
                        return;
                    }
                    link.classList.add('active');
                    activateParentMenu(link);
                }
            });

            // Helper function to activate parent menu and expand collapse
            function activateParentMenu(link) {
                const parentCollapse = link.closest('.collapse');
                if (parentCollapse) {
                    parentCollapse.classList.add('show');
                    const parentToggle = parentCollapse.previousElementSibling;
                    if (parentToggle && parentToggle.classList.contains('menu-toggle')) {
                        // Keep parent toggle expanded but DO NOT mark it as 'active'.
                        parentToggle.classList.remove('collapsed');
                        parentToggle.setAttribute('aria-expanded', 'true');
                    }
                }
            }

            const collapses = document.querySelectorAll('#sidebarMenu .collapse');
            collapses.forEach(collapse => {
                const toggle = collapse.previousElementSibling;
                if (!toggle) {
                    return;
                }

                collapse.addEventListener('shown.bs.collapse', function() {
                    // When expanded, only remove the 'collapsed' state; do not add 'active'
                    toggle.classList.remove('collapsed');
                });

                collapse.addEventListener('hidden.bs.collapse', function() {
                    // When collapsed, restore collapsed state. Parent toggle stays without 'active'.
                    toggle.classList.add('collapsed');
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>

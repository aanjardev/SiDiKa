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

    <!-- @vite([
          'resources/admin_theme/css/core/libs.min.css',
          'resources/admin_theme/vendor/aos/dist/aos.css',
          'resources/admin_theme/css/hope-ui.min.css',
          'resources/admin_theme/css/custom.min.css',
          'resources/admin_theme/css/dark.min.css',
          'resources/admin_theme/css/customizer.min.css',
          'resources/admin_theme/css/rtl.min.css'
      ]) -->


    <link rel="shortcut icon" href="{{ $setting?->logo_url ?? asset('mainIMG/logoDK.png') }}" type="image/png">

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
    <style>
        * {
    font-family: "Montserrat", sans-serif;
}

:root {
    --sidebar-width: 260px;
    --sidebar-bg: #ffffff;
    --sidebar-hover: #f5f7fb;
    --primary-color: #4e6bff;
    /* Brand blue used specifically for sidebar hover/active to avoid being overridden */
    --brand-blue: #4e6bff;
    --text-dark: #2f353f;
    --text-muted: #6c757d;
    --border-color: #e9ecef;
}

body {
    background-color: #f8f9fa;
    overflow-x: hidden;
}

/* Sidebar */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: var(--sidebar-width);
    background: var(--sidebar-bg);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
    z-index: 1000;
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}
.sidebar::-webkit-scrollbar {
    width: 6px;
}
.sidebar::-webkit-scrollbar-track {
    background: transparent;
}
.sidebar::-webkit-scrollbar-thumb {
    background: #dee2e6;
    border-radius: 3px;
}
.sidebar::-webkit-scrollbar-thumb:hover {
    background: #adb5bd;
}

.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
}
.sidebar-logo {
    width: 50px;
    height: 50px;
    object-fit: contain;
}
.sidebar-brand {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.sidebar-brand-text {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: -0.02em;
}
.sidebar-brand-subtitle {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin: 0;
}

.sidebar-menu {
    padding: 1rem 0 2rem;
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
}
.menu-section {
    padding: 0 0.75rem 1rem;
}

.menu-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    margin: 0;
    width: 100%;
    background: transparent;
    color: var(--text-dark);
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.92rem;
    transition: color 0.2s, background-color 0.2s, box-shadow 0.25s,
        transform 0.15s;
    border: none;
    cursor: pointer;
}
.menu-link:hover {
    background: var(--brand-blue);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(78, 107, 255, 0.15);
}

/* Active state for top-level menu links (e.g., Dashboard) */
.menu-link.active {
    background: var(--brand-blue);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(78, 107, 255, 0.15);
}
.menu-link.active .menu-icon,
.menu-link.active .menu-arrow {
    color: #fff;
}
.menu-icon {
    width: 22px;
    font-size: 1rem;
    color: var(--text-muted);
    transition: color 0.2s;
    text-align: center;
}
.menu-arrow {
    margin-left: auto;
    font-size: 0.8rem;
    color: var(--text-muted);
    transition: transform 0.3s ease, color 0.2s ease;
}
.menu-link:hover .menu-icon,
.menu-link:hover .menu-arrow {
    color: #fff;
}

/* Remove hover blue on sub header toggles only */
.menu-toggle:hover {
    background: transparent;
    color: var(--text-dark);
    box-shadow: none;
    transform: none;
}
.menu-toggle:hover .menu-icon,
.menu-toggle:hover .menu-arrow {
    color: var(--text-muted);
}

/* Active state for collapse toggles (when child submenu contains active link) */
.menu-toggle.active {
    background: var(--brand-blue);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(78, 107, 255, 0.12);
}
.menu-toggle.active .menu-icon,
.menu-toggle.active .menu-arrow {
    color: #fff;
}

.menu-arrow {
    margin-left: auto;
    font-size: 0.75rem;
    transition: transform 0.3s ease;
}
.menu-link[data-bs-toggle="collapse"]:not(.collapsed) .menu-arrow {
    transform: rotate(90deg);
}

/* Submenu */
.submenu {
    padding-left: 0;
    margin-top: 0.5rem;
}
.submenu-item {
    margin: 0.25rem 0;
}
.submenu-link {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.6rem 1rem 0.6rem 3rem;
    margin: 0 0.25rem;
    background: #ffffff;
    color: var(--text-dark);
    text-decoration: none;
    border-radius: 10px;
    font-size: 0.78rem;
    font-weight: 500;
    transition: color 0.2s, background-color 0.2s, padding-left 0.2s,
        box-shadow 0.2s;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.submenu-link:hover {
    background: var(--brand-blue);
    color: #fff;
    padding-left: 3.2rem;
    box-shadow: 0 10px 20px rgba(78, 107, 255, 0.12);
}
.submenu-link.active {
    background: var(--brand-blue);
    color: #fff;
    font-weight: 600;
    box-shadow: 0 12px 24px rgba(78, 107, 255, 0.15);
}
.submenu-link.active .submenu-icon {
    color: #fff;
}
.submenu-link.active:hover {
    background: var(--brand-blue);
    color: #fff;
}
.submenu-icon {
    width: 18px;
    font-size: 0.95rem;
    color: var(--text-muted);
    transition: color 0.2s;
    text-align: center;
}
.submenu-link:hover .submenu-icon {
    color: #fff;
}

/* Footer */
.sidebar-footer {
    position: relative;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 1rem;
    background: var(--sidebar-bg);
    border-top: 1px solid var(--border-color);
    flex-shrink: 0;
    margin-top: auto;
}
.user-profile {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 8px;
    background: var(--sidebar-hover);
    cursor: pointer;
    transition: all 0.3s ease;
}
.user-profile:hover {
    background: #e9ecef;
}
.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--brand-blue);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.95rem;
}
.user-info {
    flex: 1;
}
.user-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-dark);
    margin: 0;
    line-height: 1.2;
}
.user-role {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin: 0;
}

/* Sidebar footer dropdown */
.sidebar-footer {
    position: relative;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 1rem;
    background: var(--sidebar-bg);
    border-top: 1px solid var(--border-color);
    flex-shrink: 0;
    margin-top: auto;
    display: flex;
    justify-content: center;
}
.sidebar-footer .dropdown {
    position: relative;
    display: inline-block;
    width: auto;
}
.sidebar-footer .dropdown-toggle {
    border: 1px solid #e8eef6;
    background: #fff;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
    padding-right: 0.85rem;
}
.sidebar-footer .dropdown-toggle::after {
    display: none;
}
.sidebar-footer .dropdown-toggle:focus {
    box-shadow: 0 0 0 3px rgba(78, 107, 255, 0.12);
}
.sidebar-footer .sidebar-profile-toggle {
    min-width: 220px;
    width: auto;
}
.sidebar-footer .dropdown-menu {
    min-width: 100%;
    width: auto;
    border: 1px solid #e8eef6;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
    border-radius: 12px;
    left: 0 !important;
    right: auto !important;
}
.sidebar-footer .sidebar-profile-menu {
    min-width: 100%;
    width: auto;
    max-width: none;
}
/* Main content */
.main-content {
    margin-left: var(--sidebar-width);
    min-height: 100vh;
    padding: 2.25rem 2.5rem;
    transition: margin-left 0.3s ease, padding 0.3s ease;
}
.navbar-top {
    background: #fff;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
}

/* Mobile */
@media (max-width: 1200px) {
    :root {
        --sidebar-width: 240px;
    }
}

@media (max-width: 991px) {
    .sidebar {
        width: min(92vw, 360px);
        max-height: 88vh;
        height: auto;
        top: 16px;
        bottom: auto;
        border-radius: 18px;
        left: 50%;
        right: auto;
        transform: translate(-50%, -120%);
        box-shadow: 0 24px 55px rgba(15, 23, 42, 0.28);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .sidebar.show {
        transform: translate(-50%, 0);
    }
    .main-content {
        margin-left: 0;
        padding: 4.5rem 1.5rem 2rem;
    }
    .sidebar-toggle-mobile {
        display: flex;
    }
}

.sidebar-toggle-mobile {
    display: none;
    align-items: center;
    justify-content: center;
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: var(--brand-blue);
    color: #fff;
    border: none;
    box-shadow: 0 18px 40px rgba(78, 107, 255, 0.28);
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    z-index: 1100;
}
.sidebar-toggle-mobile:hover {
    background: #3a59ff;
    transform: translateY(-2px);
    box-shadow: 0 22px 45px rgba(78, 107, 255, 0.32);
}
.sidebar-toggle-mobile:active {
    transform: translateY(0);
    box-shadow: 0 12px 25px rgba(78, 107, 255, 0.25);
}
.sidebar-toggle-mobile i {
    font-size: 1.45rem;
}

.sidebar-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.35);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    z-index: 1030;
}
.sidebar-overlay.show {
    opacity: 1;
    visibility: visible;
}

/* Loader */
#loading {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}
.loader {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--brand-blue);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

/* Variables */
:root {
    --primary-color: #2f353f;
    --secondary-color: #130303;
    --text-color: #1a1a1a;
    --gray-100: #f8f9fa;
    --gray-200: #e9ecef;
    --gray-300: #dee2e6;
    --gray-400: #ced4da;
    --gray-500: #adb5bd;
    --gray-600: #6c757d;
    --gray-700: #495057;
    --gray-800: #343a40;
    --gray-900: #212529;
    --bs-gray: #6c757d;
    --bs-gray-shade-80: #161719;
    --bs-gray-shade-60: #2b2f32;
    --bs-gray-shade-40: #41464b;
    --bs-gray-shade-20: #565e64;
    --bs-gray-tint-90: #f0f1f2;
    --bs-gray-tint-80: #e2e3e5;
    --bs-gray-tint-60: #c4c8cb;
    --bs-gray-tint-40: #a7acb1;
    --bs-gray-tint-20: #899197;
    --bs-gray-rgb: 108, 117, 125;
    --bs-heading-color: #0a0c0d;
    --bs-gray-dark: #343a40;
    --bs-gray-dark-shade-80: #0a0c0d;
    --bs-gray-dark-shade-60: #15171a;
    --bs-gray-dark-shade-40: #1f2326;
    --bs-gray-dark-shade-20: #2a2e33;
    --bs-gray-dark-tint-90: #ebebec;
    --bs-gray-dark-tint-80: #d6d8d9;
    --bs-gray-dark-tint-60: #aeb0b3;
    --bs-gray-dark-tint-40: #85898c;
    --bs-gray-dark-tint-20: #5d6166;
    --danger: #dc3545;
    --success: #198754;
    --warning: #ffc107;
    --info: #0dcaf0;
    --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
    --transition-base: all 0.3s ease;
}

/* Base Styles */
* {
    font-family: "Montserrat", sans-serif;
    box-sizing: border-box;
    margin: 1;
    padding: 0;
}

body {
    background-color: var(--bs-gray-dark-tint-90);
    color: var(--text-color);
    line-height: 1.6;
}

/* Typography */
h1,
h2,
h3,
h4,
h5,
h6 {
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

h1 {
    font-size: 2.5rem;
    font-weight: 800;
    padding-bottom: 0.75rem;
    margin-bottom: 1.5rem;
}

h2 {
    font-size: 2rem;
}

p {
    color: var(--gray-700);
    margin-bottom: 1rem;
}

/* Layout */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

main {
    margin: 2rem 0;
    min-height: calc(100vh - 200px);
}

section {
    margin-bottom: 2rem;
}

/* Cards */
.admin-card {
    background: white;
    border-radius: 10px;
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-md);
    transition: var(--transition-base);
    overflow: hidden;
}

.admin-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.card-header {
    background-color: var(--primary-color);
    color: white;
    padding: 1.25rem;
    border-bottom: none;
}

.card-header h2 {
    color: white;
    margin: 0;
    font-size: 1.5rem;
}

.card-body {
    padding: 1.5rem;
}

/* Forms */
.form-label {
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
}

.form-control {
    border: 1px solid var(--gray-300);
    border-radius: 6px;
    padding: 0.75rem;
    transition: var(--transition-base);
}

.form-control:focus {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 0.2rem rgba(78, 205, 196, 0.25);
}

/* Tables */
.table {
    margin-bottom: 0;
}

.table th {
    background-color: var(--primary-color);
    color: white;
    font-weight: 600;
    border: none;
    padding: 1rem;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
    color: var(--gray-700);
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: var(--gray-100);
}

.table-hover tbody tr:hover {
    background-color: var(--gray-200);
}

/* Buttons */
.btn {
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    transition: var(--transition-base);
}

.btn-success {
    background-color: var(--success);
    border: none;
}

.btn-success:hover {
    background-color: #146c43;
    transform: translateY(-2px);
}

.btn-warning {
    background-color: var(--warning);
    border: none;
    color: var(--gray-900);
}

.btn-warning:hover {
    background-color: #ffca2c;
    transform: translateY(-2px);
}

.btn-danger {
    background-color: var(--danger);
    border: none;
}

.btn-danger:hover {
    background-color: #bb2d3b;
    transform: translateY(-2px);
}

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.875rem;
}

/* Alerts */
.alert {
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border: none;
}

.alert-success {
    background-color: #d1e7dd;
    color: #0f5132;
}

/* Responsive Design */
@media (max-width: 768px) {
    h1 {
        font-size: 2rem;
    }

    .card-header h2 {
        font-size: 1.25rem;
    }

    .table {
        font-size: 0.875rem;
    }

    .btn {
        padding: 0.5rem 1rem;
    }

    .form-control {
        padding: 0.5rem;
        background-color: #47747a;
    }
}

@media (max-width: 576px) {
    .container {
        padding: 0 0.5rem;
    }

    h1 {
        font-size: 1.75rem;
    }

    .card-body {
        padding: 1rem;
    }

    .table-responsive {
        margin: 0 -1rem;
    }
}

.navbar-dark .navbar-nav .nav-link {
    color: white;
    font-weight: 600;
    transition: border-bottom 0.3s ease;
}

.navbar-dark .navbar-nav .nav-link:hover {
    border-bottom: 1px solid white;
}

.text-gray {
    color: #6b7280;
}

.text-pink {
    color: #db2777;
}

.text-uppercase {
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.1em;
}

.map-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    height: 400px;
    transition: box-shadow 0.3s ease;
}

.map-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.contact-card,
.contact-details-card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.contact-card:hover,
.contact-details-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.contact-details-card {
    padding: 1.5rem;
}

.contact-card .form-label,
.contact-details-card .form-label {
    font-weight: 600;
}

.contact-card .form-control,
.contact-details-card .form-control {
    border-radius: 4px;
    padding: 0.5rem;
}

.contact-card .btn-danger {
    background-color: #dc3545;
    border: none;
    padding: 0.75rem 1.5rem;
    font-weight: 700;
    transition: background-color 0.3s ease;
}

.contact-card .btn-danger:hover {
    background-color: #bb2d3b;
}

footer a:hover {
    border-bottom: 1px solid white;
}

.logo-medplace img {
    max-width: 60px;
}

.whatsapp-float {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}
.whatsapp-float img {
    width: 60px;
    height: 60px;
    transition: transform 0.3s ease;
}
.whatsapp-float:hover img {
    transform: scale(1.1);
}
.whatsapp-float span {
    display: none;
    position: absolute;
    bottom: 70px;
    right: 0;
    background-color: #25d366;
    color: white;
    padding: 5px 15px;
    border-radius: 5px;
    font-size: 14px;
}
.whatsapp-float:hover span {
    display: block;
    transition-duration: 500ms;
}

/* Media Query untuk Responsivitas */

/* iPad (tablet, lebar 768px - 1024px) */
@media (max-width: 1024px) {
    .container {
        max-width: 100%;
        padding: 0 15px;
    }

    main {
        margin-top: 60px;
    }

    h2 {
        font-size: 1.75rem;
    }

    h4 {
        font-size: 1.15rem;
    }

    p {
        font-size: 0.85rem;
    }

    .map-card {
        height: 300px;
    }

    .contact-card,
    .contact-details-card {
        padding: 1.25rem;
    }
}

/* HP (mobile, lebar <= 576px) */
@media (max-width: 576px) {
    h2 {
        font-size: 1.5rem;
    }

    h4 {
        font-size: 1rem;
    }

    p {
        font-size: 0.75rem;
    }

    .row {
        flex-direction: column;
    }

    .map-card {
        height: 250px;
        margin-bottom: 1rem;
    }

    .contact-details-card,
    .contact-card {
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .contact-card .form-control,
    .contact-card .form-select {
        font-size: 0.875rem;
    }

    .logo-medplace img {
        max-width: 50px;
    }

    footer .row {
        text-align: center;
    }

    footer .logo-medplace {
        justify-content: center;
    }
}

    </style>
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/alert.js') }}"></script>


<body class="  ">
    @unless ($__env->hasSection('disable_alerts'))
        @include('partials.alerts')
    @endunless
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

                    <div class="submenu-item">
                        <a href="{{ route('admin.smart-stock.index') }}" class="submenu-link">
                            <i class="fas fa-chart-line submenu-icon"></i>
                            <span>Smart Stock Analysis</span>
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
                            <span>Manajemen Akses</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Footer - User Profile -->
        <div class="sidebar-footer">
            <div class="dropdown dropup">
                <button class="user-profile dropdown-toggle sidebar-profile-toggle text-start" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info">
                        <p class="user-name">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="user-role">{{ isset(Auth::user()->role) ? ucfirst(Auth::user()->role) : '' }}</p>
                    </div>
                    <i class="fas fa-chevron-down" style="color: var(--text-muted); font-size: 0.8rem;"></i>
                </button>

                <!-- Dropdown Menu -->
                <ul class="dropdown-menu dropdown-menu-end sidebar-profile-menu" style="margin: 0.35rem 0;">
                    <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button type="button"
                                class="dropdown-item text-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#logoutConfirmModal">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </button>
                    </li>
                </ul>
            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Form Validation JS -->
    <script src="{{ asset('js/form-validation.js') }}"></script>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="logoutConfirmLabel">
                        <i class="fa-solid fa-right-from-bracket me-2 text-warning"></i>Konfirmasi Logout
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center text-warning mx-auto mb-3"
                             style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-right-from-bracket fa-2x"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Apakah Anda yakin ingin logout?</h6>
                        <p class="text-muted small mb-0">
                            Anda akan keluar dari sistem dan perlu login kembali untuk mengakses halaman admin.
                        </p>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-times me-2"></i>Batal
                    </button>
                    <form id="logoutFormLayout" action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fa-solid fa-right-from-bracket me-2"></i>Ya, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

                const isProductPhotoUpload = /^\/admin\/products\/\d+\/photos-upload(\/)?$/i.test(currentPath);
                // If we're on a product photo upload page, force the "Foto Produk" submenu active
                if (isProductPhotoUpload && linkPath === '{{ route('admin.products.photos') }}'.replace(window.location.origin, '')) {
                    link.classList.add('active');
                    activateParentMenu(link);
                    return;
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
                    if (
                        linkPath === '/admin/products' &&
                        (currentPath.startsWith('/admin/products/photos') ||
                            /\/admin\/products\/\d+\/photos-upload/.test(currentPath))
                    ) {
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


<!doctype html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>@yield('title', 'Admin Panel') | SiDiKa</title>

      @vite([
          'resources/admin_theme/css/core/libs.min.css',
          'resources/admin_theme/vendor/aos/dist/aos.css',
          'resources/admin_theme/css/hope-ui.min.css',
          'resources/admin_theme/css/custom.min.css',
          'resources/admin_theme/css/dark.min.css',
          'resources/admin_theme/css/customizer.min.css',
          'resources/admin_theme/css/rtl.min.css'
      ])

    @stack('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
    .iq-navbar {
        background: var(--bs-body-bg) !important;
        box-shadow: none !important;
    }

    .iq-navbar .navbar-inner {
        min-height: 60px !important;
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
    }

    .iq-navbar .navbar-inner h1 {
        font-size: 1.75rem;
    }
</style>

  </head>
  <body class="  ">
    <!-- loader Start -->
    <div id="loading">
      <div class="loader simple-loader">
          <div class="loader-body"></div>
      </div>    </div>
    <!-- loader END -->

    <aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all ">
        <div class="sidebar-header d-flex align-items-center justify-content-start">
            <a href="{{ url('/admin/index') }}" class="navbar-brand"> {{-- Arahkan ke route dashboard admin Anda --}}
                <svg width="30" class="" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2" transform="rotate(-45 -0.757324 19.2427)" fill="currentColor"/>
                    <rect x="7.72803" y="27.728" width="28" height="4" rx="2" transform="rotate(-45 7.72803 27.728)" fill="currentColor"/>
                    <rect x="10.5366" y="16.3945" width="16" height="4" rx="2" transform="rotate(45 10.5366 16.3945)" fill="currentColor"/>
                    <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2" transform="rotate(45 10.5562 -0.556152)" fill="currentColor"/>
                </svg>
                <h4 class="logo-title">SiDiKa</h4>
            </a>
            <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
                <i class="icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </i>
            </div>
        </div>
        <div class="sidebar-body pt-0 data-scrollbar">
            <div class="sidebar-list">
                <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
                    <li class="nav-item static-item">
                        <a class="nav-link static-item disabled" tabindex="-1">
                            <span class="default-icon">Home</span>
                            <span class="mini-icon">-</span>
                        </a>
                    </li>
                    <li class="nav-item">

                        <a class="nav-link active" aria-current="page" href="#">
                            <i class="icon">
                                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  {{-- ... (svg icon) ... --}}
                                </svg>
                            </i>
                            <span class="item-name">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        {{-- Ganti href ke route yg sesuai, misal: {{ route('admin.products.index') }} --}}
                        <a class="nav-link" href="#">
                            <i class="icon">
                                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                   {{-- ... (svg icon) ... --}}
                                </svg>
                            </i>
                            <span class="item-name">Produk</span>
                        </a>
                    </li>

                </ul>
                </div>
        </div>
        <div class="sidebar-footer"></div>
    </aside>

    <main class="main-content">
        <div class="position-relative">
            <nav class="nav navbar navbar-expand-lg navbar-light iq-navbar">
                <div class="container-fluid navbar-inner">

                <a href="{{ url('/admin/index') }}" class="navbar-brand d-lg-none">
                    <svg width="30" class="" viewBox="0 0 30 30" ...> ... </svg> {{-- SVG Logo --}}
                    <h4 class="logo-title">SiDiKa</h4>
                </a>

                <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
                    <i class="icon">
                    <svg width="20px" height="20px" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
                    </svg>
                    </i>
                </div>

                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <h1 class="ms-3">@yield('title')</h1>
                </div>

                </div>
            </nav>
        </div>




      <div class="conatiner-fluid content-inner mt-4 py-0">
        @yield('content')
      </div>

        <footer class="footer">
          <div class="footer-body">
              <ul class="left-panel list-inline mb-0 p-0">
                  <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
                  <li class="list-inline-item"><a href="#">Terms of Use</a></li>
              </ul>
              <div class="right-panel">
                  ©<script>document.write(new Date().getFullYear())</script> SiDiKa, Made with
                  <span class="text-danger">
                      <svg width="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M15.85 2.50065C16.481 2.50065 17.111 2.58965 17.71 2.79065C21.401 3.99065 22.731 8.04065 21.62 11.5806C20.95 13.8406 19.58 15.6506 17.82 17.0606C16.16 18.3706 14.16 19.3806 12.01 20.3306C11.95 20.3506 11.88 20.3506 11.82 20.3306C9.67 19.3806 7.67 18.3706 6.01 17.0606C4.25 15.6506 2.88 13.8406 2.21 11.5806C1.1 8.04065 2.43 3.99065 6.12 2.79065C6.719 2.58965 7.349 2.50065 7.98 2.50065C8.65 2.50065 9.3 2.59965 9.93 2.82065C10.43 3.00065 10.87 3.26065 11.26 3.59065C11.45 3.78065 11.7 3.93065 11.9 4.01065C11.96 4.03065 12.03 4.03065 12.09 4.01065C12.29 3.93065 12.54 3.78065 12.73 3.59065C13.12 3.26065 13.56 3.00065 14.06 2.82065C14.69 2.59965 15.34 2.50065 15.85 2.50065Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      </svg>
                  </span> by <a href="https://github.com/aanjardev">aanjardev</a>.
              </div>
          </div>
      </footer>
   </main>


<script src="{{ asset('admin_theme/js/core/libs.min.js') }}"></script>
<script src="{{ asset('admin_theme/js/core/external.min.js') }}"></script>

<script src="{{ asset('admin_theme/js/hope-ui.js') }}" defer></script>

    @stack('scripts')
  </body>
</html>

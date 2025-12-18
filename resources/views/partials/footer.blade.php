<!-- Footer -->
<footer id="main-footer" class="footer-area">
    @php
        $setting = \App\Models\CatalogSettings::first();
        $branches = \App\Models\Branch::with('jamOperasional')
            ->where('is_active', true)
            ->get();
    @endphp
    <div class="container">
        <div class="row">
            <!-- Company Description -->
            <div class="col-lg-3 col-md-6 col-12 mb-4 mb-lg-0">
                <div class="footer-widget">
                    <h4 class="widget-title">{{$setting->nama_website}}</h4>
                    <p class="company-desc">{{$setting->description}}</p>
                    <div class="social-links mt-3">
                        <a href="{{$setting->facebook_link}}" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="{{$setting->instagram_link}}" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="{{$setting->youtube_link}}" class="social-link"><i class="fab fa-youtube"></i></a>
                        <a href="{{$setting->tiktok_link}}" class="social-link"><i class="fab fa-tiktok"></i></a>
                    {{-- </div> --}}
                    {{-- <div class="d-flex justify-content-start gap-3 mt-3"> --}}
                        <!-- Tokopedia -->
                        <a href="{{$setting->tokopedia_link}}" target="_blank" rel="noopener" class="social-link" title="Tokopedia">
                            <img src="https://i0.wp.com/x-mos.com/wp-content/uploads/2020/11/logo-tokopedia-icon-mascot-400x400-copy.png?fit=400%2C400&ssl=1&w=640" alt="Tokopedia" style="width:25px;height:25px;object-fit:contain;">
                        </a>
                        <!-- Shopee -->
                        <a href="{{$setting->shopee_link}}" target="_blank" rel="noopener" class="social-link" title="Shopee">
                            <img src="https://img.icons8.com/?size=100&id=OO5wGWyvSK0L&format=png&color=FFFFFF" alt="Shopee" style="width:20px;height:20px;object-fit:contain;">
                        </a>
                    </div>
                </div>
            </div>

            @foreach($branches as $branch)
            @php
                $hariMap = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
                $todayIndo = $hariMap[now()->format('l')] ?? now()->translatedFormat('l');
                $todayRow = $branch->jamOperasional->firstWhere('hari', $todayIndo);
                if ($todayRow && $todayRow->is_buka) {
                    $slot = ($todayRow->jam_buka && $todayRow->jam_tutup)
                        ? \Illuminate\Support\Str::of($todayRow->jam_buka)->beforeLast(':') . ' - ' . \Illuminate\Support\Str::of($todayRow->jam_tutup)->beforeLast(':')
                        : 'Buka';
                } else {
                    $slot = 'Tutup';
                }
                $mapLink = $branch->link_maps ?: 'https://www.google.com/maps?q=' . urlencode($branch->nama . ' ' . $branch->alamat);
            @endphp
            <div class="col-lg-3 col-sm-6 col-12 mb-4 mb-lg-0">
                <div class="footer-widget">
                    <h4 class="widget-title">{{ $branch->nama }}</h4>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-clock"></i>
                            <div class="d-flex flex-column">
                                <button class="btn p-0 text-start d-inline-flex align-items-center justify-content-start gap-2 w-100"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#jamFooter{{ $loop->index }}"
                                    aria-expanded="false"
                                    aria-controls="jamFooter{{ $loop->index }}">
                                    <span class="text-white">Hari ini ({{ $todayIndo }}): {{ $slot }} <i class="fas fa-chevron-down small text-white" style="margin-top:-3px"></i></span>
                                </button>
                                <div class="collapse mt-1" id="jamFooter{{ $loop->index }}">
                                    <ul class="list-unstyled mb-0 small">
                                        @foreach($branch->jamOperasional as $jam)
                                        @php
                                            $slotHarian = $jam->is_buka
                                                ? (\Illuminate\Support\Str::of($jam->jam_buka)->beforeLast(':') . ' - ' . \Illuminate\Support\Str::of($jam->jam_tutup)->beforeLast(':'))
                                                : 'Tutup';
                                        @endphp
                                        <li class="d-flex justify-content-between">
                                            <span class="text-white-50">{{ $jam->hari }}</span>
                                            <span class="text-white-50">{{ $slotHarian }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <a href="tel:{{ preg_replace('/[^0-9+]/','', $branch->nomor_telepon) }}">{{ $branch->nomor_telepon }}</a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $branch->alamat }}</span>
                        </li>
                        <li>
                            <i class="fas fa-location-arrow"></i>
                            <a href="{{ $mapLink }}" target="_blank">Lihat di Google Maps</a>
                        </li>
                    </ul>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Copyright -->
        <div class="footer-bottom">
            <div class="row">
                <div class="col-12">
                    <div class="copyright text-center">
                        <p>&copy; {{ date('Y') }} Dinoyo Kamera. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Footer styles -->
<link rel="stylesheet" href="{{ asset('css/footer.css') }}">

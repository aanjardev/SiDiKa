<!-- Footer -->
<footer id="main-footer" class="footer-area">
    @php
        $setting = \App\Models\CatalogSettings::first();
    @endphp
    <div class="container">
        <div class="row">
            <!-- Company Description -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="footer-widget">
                    <h4 class="widget-title">{{$setting->nama_website}}</h4>
                    <p class="company-desc">{{$setting->description}}</p>
                    <div class="social-links mt-3">
                        <a href="{{$setting->facebook_link}}" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="{{$setting->instagram_link}}" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="{{$setting->youtube_link}}" class="social-link"><i class="fab fa-youtube"></i></a>
                        <a href="{{$setting->tiktok_link}}" class="social-link"><i class="fab fa-tiktok"></i></a>
                    </div>
                    <div class="d-flex justify-content-start gap-3 mt-3">
                        <!-- Tokopedia -->
                        <a href="{{$setting->tokopedia_link}}" target="_blank" rel="noopener" class="social-link" title="Tokopedia">
                            <img src="https://i0.wp.com/x-mos.com/wp-content/uploads/2020/11/logo-tokopedia-icon-mascot-400x400-copy.png?fit=400%2C400&ssl=1&w=640" alt="Tokopedia" style="width:28px;height:28px;object-fit:contain;">
                        </a>
                        <!-- Shopee -->
                        <a href="{{$setting->shopee_link}}" target="_blank" rel="noopener" class="social-link" title="Shopee">
                            <img src="https://pngmax.com/_next/image?url=https%3A%2F%2Fpng-max.s3.ap-south-1.amazonaws.com%2Flow%2Fc7f66c3b-d50f-4afa-9090-052a98e8a27a.png&w=1200&q=75" alt="Shopee" style="width:28px;height:28px;object-fit:contain;">
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dinoyo Kamera 1 -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="footer-widget">
                    <h4 class="widget-title">Dinoyo Kamera 1</h4>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Setiap Hari - 10.00 - 20.00 WIB</span>
                        </li>
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <a href="tel:+6282345670014">+62 823-4567-0014</a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>RUKO TLOGOMAS SQUARE kav 9 lantai 1, Tlogomas, Kec Lowokwaru, Kota Malang, Jawa Timur, 65144</span>
                        </li>
                        <li>
                            <i class="fas fa-location-arrow"></i>
                            <a href="https://g.co/kgs/HREiPqs" target="_blank">Lihat di Google Maps</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Dinoyo Kamera 2 -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="footer-widget">
                    <h4 class="widget-title">Dinoyo Kamera 2</h4>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Rabu - Senin (08.00 - 18.00)<br>Selasa Tutup</span>
                        </li>
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <a href="tel:+6281230551552">+62 812-3055-1552</a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Jl. C.Trowulan No.65B, Mojolangu, Kec. Lowokwaru, Kota Malang, Jawa Timur 65142</span>
                        </li>
                        <li>
                            <i class="fas fa-location-arrow"></i>
                            <a href="https://g.co/kgs/siaqyp4" target="_blank">Lihat di Google Maps</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Toko Kamera Pasuruan -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="footer-widget">
                    <h4 class="widget-title">Toko Kamera Pasuruan</h4>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Setiap Hari - 10.00 - 20.00 WIB</span>
                        </li>
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <a href="tel:+6282345670014">+62 823-4567-0014</a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Jl. Sunan Ampel, Petamanan, Kec. Bugul Kidul, Kota Pasuruan, Jawa Timur</span>
                        </li>
                        <li>
                            <i class="fas fa-location-arrow"></i>
                            <a href="https://g.co/kgs/BR1Mnbn" target="_blank">Lihat di Google Maps</a>
                        </li>
                    </ul>
                </div>
            </div>
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
@extends('layouts.customer')

@section('title', $content['title'] . ' | Dinoyo Kamera')
@section('body-class', 'dinoyo-way-body')
@section('lang', 'id')

@push('meta')
    <meta name="description" content="{{ $content['description'] }}">
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/dinoyo-way.css') }}">
@endpush

@section('content')
<main class="dinoyo-way" id="dinoyo-way">
    <section class="way-hero" aria-labelledby="way-title">
        <img class="way-hero-image" src="{{ $heroImageUrl }}" alt="{{ $content['hero']['image_alt'] }}" fetchpriority="high">
        <div class="way-hero-shade" aria-hidden="true"></div>
        <div class="way-viewfinder" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
        <div class="container way-hero-inner">
            <div class="way-hero-copy">
                <p class="way-eyebrow way-hero-eyebrow">{{ $content['hero']['eyebrow'] }}</p>
                <h1 id="way-title">{{ $content['hero']['title'] }}</h1>
                <p class="way-hero-statement">{{ $content['hero']['statement'] }}</p>
                <p class="way-hero-description">{{ $content['hero']['description'] }}</p>
                <a href="#filosofi" class="way-button way-button-primary">Mulai perjalanan <i class="bi bi-arrow-down-right" aria-hidden="true"></i></a>
            </div>
            <p class="way-hero-caption">{{ $content['hero']['caption'] }}</p>
        </div>
    </section>

    <nav class="way-section-nav" aria-label="{{ $content['navigation_aria_label'] }}">
        <div class="container way-section-links">
            @foreach($content['navigation'] as $item)
                <a href="#{{ $item['id'] }}">{{ $item['label'] }}</a>
            @endforeach
        </div>
    </nav>

    <section class="way-section way-philosophy" id="filosofi" aria-labelledby="philosophy-title">
        <div class="container">
            <div class="way-story-grid way-reveal">
                <figure class="way-story-photo">
                    <img src="{{ $philosophyImageUrl }}" alt="{{ $content['philosophy']['image_alt'] }}" loading="lazy" width="736" height="920">
                    <figcaption>{{ $content['philosophy']['image_caption'] }}</figcaption>
                </figure>
                <div class="way-story-copy">
                    <p class="way-eyebrow">{{ $content['philosophy']['eyebrow'] }}</p>
                    <h2 id="philosophy-title">{{ $content['philosophy']['title'] }}</h2>
                    <p class="way-lead">{{ $content['philosophy']['lead'] }}</p>
                    @foreach($content['philosophy']['paragraphs'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="way-section way-spiritual" id="landasan-spiritual" aria-labelledby="spiritual-title">
        <div class="container way-spiritual-grid">
            <div class="way-reveal">
                <p class="way-eyebrow">{{ $content['spiritual']['eyebrow'] }}</p>
                <h2 id="spiritual-title">{{ $content['spiritual']['title'] }}</h2>
                <p class="way-section-description">{{ $content['spiritual']['note'] }}</p>
            </div>
            <div class="way-verse way-reveal">
                <p class="way-verse-arabic" lang="ar" dir="rtl">
                    @foreach($content['spiritual']['arabic_segments'] as $segment)
                        <span>{{ $segment }}</span>
                    @endforeach
                </p>
                <p class="way-verse-translation">“{{ $content['spiritual']['translation'] }}”</p>
                <p class="way-verse-reference">{{ $content['spiritual']['reference'] }}</p>
            </div>
        </div>
    </section>

    <section class="way-section way-vision" id="visi-misi" aria-labelledby="vision-title">
        <div class="container">
            <header class="way-section-heading way-reveal">
                <p class="way-eyebrow">{{ $content['vision_mission']['eyebrow'] }}</p>
                <h2 id="vision-title">{{ $content['vision_mission']['title'] }}</h2>
            </header>
            <div class="way-vision-statement way-reveal">
                <p class="way-proposal-label">{{ $content['vision_mission']['vision_label'] }}</p>
                <p>{{ $content['vision_mission']['vision'] }}</p>
            </div>
            <p class="way-proposal-label way-mission-label">{{ $content['vision_mission']['mission_label'] }}</p>
            <div class="way-mission-grid-new">
                @foreach($content['vision_mission']['missions'] as $mission)
                    <article class="way-mission-card">
                        <span class="way-card-index">{{ sprintf('%02d', $loop->iteration) }}</span>
                        <h3>{{ $mission['name'] }}</h3>
                        <p>{{ $mission['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="way-section way-culture" id="budaya-organisasi" aria-labelledby="culture-title">
        <div class="container">
            <header class="way-section-heading way-reveal">
                <p class="way-eyebrow">{{ $content['culture']['eyebrow'] }}</p>
                <h2 id="culture-title">{{ $content['culture']['title'] }}</h2>
                <p>{{ $content['culture']['description'] }}</p>
            </header>
            <div class="way-culture-tabs" role="tablist" aria-label="Nilai budaya SIAPPP">
                @foreach($content['culture']['items'] as $item)
                    <button type="button" class="way-culture-tab" role="tab" id="culture-tab-{{ $loop->index }}" aria-controls="culture-panel-{{ $loop->index }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}" data-culture-tab="{{ $loop->index }}">
                        <span class="way-culture-letter">{{ $item['letter'] }}</span>
                        <span>{{ $item['name'] }}</span>
                    </button>
                @endforeach
            </div>
            <div class="way-culture-panels">
                @foreach($content['culture']['items'] as $item)
                    <article class="way-culture-panel" role="tabpanel" id="culture-panel-{{ $loop->index }}" aria-labelledby="culture-tab-{{ $loop->index }}" tabindex="0" data-culture-panel="{{ $loop->index }}" @if(!$loop->first) hidden @endif>
                        <p class="way-proposal-label">{{ $item['name'] }}</p>
                        <h3>{{ $item['meaning'] }}</h3>
                        <p><strong>Penerapan</strong>{{ $item['application'] }}</p>
                    </article>
                @endforeach
            </div>
            <div class="way-indicator-table way-reveal">
                <p class="way-proposal-label">Indikator evaluasi budaya</p>
                <div class="way-indicator-grid">
                    @foreach($content['culture']['indicators'] as $indicator)
                        <div><span>{{ $indicator['name'] }}</span><strong>{{ $indicator['indicator'] }}</strong></div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="way-section way-discipline" id="disiplin-sukses" aria-labelledby="discipline-title">
        <div class="container">
            <header class="way-section-heading way-reveal">
                <p class="way-eyebrow">{{ $content['discipline']['eyebrow'] }}</p>
                <h2 id="discipline-title">{{ $content['discipline']['title'] }}</h2>
            </header>
            <div class="way-discipline-grid">
                @foreach($content['discipline']['groups'] as $group)
                    <article class="way-discipline-panel way-reveal">
                        <span class="way-discipline-number">0{{ $loop->iteration }}</span>
                        <h3>{{ $group['name'] }}</h3>
                        <ul>
                            @foreach($group['items'] as $item)
                                <li><i class="bi bi-check2" aria-hidden="true"></i>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="way-section way-dk" id="dk-way" aria-labelledby="dk-title">
        <div class="container">
            <header class="way-section-heading way-reveal">
                <p class="way-eyebrow">{{ $content['dk_way']['eyebrow'] }}</p>
                <h2 id="dk-title">{{ $content['dk_way']['title'] }}</h2>
            </header>
            <div class="way-dk-definition way-reveal">
                <p class="way-proposal-label">{{ $content['dk_way']['definition_label'] }}</p>
                <p>{{ $content['dk_way']['definition'] }}</p>
            </div>
            <div class="way-dk-grid">
                <div class="way-dk-how way-reveal">
                    <p class="way-proposal-label">{{ $content['dk_way']['how_to_label'] }}</p>
                    <ol>
                        @foreach($content['dk_way']['how_to'] as $step)
                            <li><span>{{ sprintf('%02d', $loop->iteration) }}</span><div><h3>{{ $step['title'] }}</h3><p>{{ $step['description'] }}</p></div></li>
                        @endforeach
                    </ol>
                </div>
                <div class="way-gratitude way-reveal">
                    <p class="way-proposal-label">{{ $content['dk_way']['winning_label'] }}</p>
                    <figure class="way-gratitude-art" aria-label="Winning the day with gratitude dan Jihad Dhuha">
                        <svg viewBox="0 0 640 420" role="img" aria-labelledby="gratitude-art-title gratitude-art-desc">
                            <title id="gratitude-art-title">Winning the day with gratitude</title>
                            <desc id="gratitude-art-desc">Ilustrasi editorial matahari pagi, garis horizon, orbit kebiasaan, dan penanda waktu Jihad Dhuha.</desc>
                            <defs>
                                <linearGradient id="gratitude-sky" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#11242d"/><stop offset=".55" stop-color="#203b46"/><stop offset="1" stop-color="#304e58"/></linearGradient>
                                <radialGradient id="gratitude-sun"><stop stop-color="#fff1ce"/><stop offset=".24" stop-color="#ffc989"/><stop offset=".62" stop-color="#ef795f" stop-opacity=".72"/><stop offset="1" stop-color="#ef795f" stop-opacity="0"/></radialGradient>
                                <linearGradient id="gratitude-ground" x1="0" y1="0" x2="0" y2="1"><stop stop-color="#172b33"/><stop offset="1" stop-color="#091419"/></linearGradient>
                                <filter id="gratitude-glow" x="-80%" y="-80%" width="260%" height="260%"><feGaussianBlur stdDeviation="14"/></filter>
                                <clipPath id="gratitude-sky-clip"><rect width="640" height="282"/></clipPath>
                            </defs>
                            <rect width="640" height="420" rx="24" fill="url(#gratitude-sky)"/>
                            <g clip-path="url(#gratitude-sky-clip)">
                                <circle cx="310" cy="194" r="138" fill="url(#gratitude-sun)" filter="url(#gratitude-glow)" opacity=".65"/>
                                <circle cx="310" cy="194" r="58" fill="url(#gratitude-sun)"/>
                            </g>
                            <g fill="none" stroke="#ffd8a1" stroke-linecap="round">
                                <path d="M160 194 A150 150 0 0 1 460 194" stroke-opacity=".18" stroke-width="2"/>
                                <path d="M190 194 A120 120 0 0 1 430 194" stroke-opacity=".32" stroke-width="2" stroke-dasharray="1 12"/>
                                <path d="M222 194 A88 88 0 0 1 398 194" stroke-opacity=".58" stroke-width="2"/>
                            </g>
                            <path d="M0 280 C120 238 195 260 290 238 S478 236 640 270 V420 H0Z" fill="url(#gratitude-ground)"/>
                            <path d="M0 284 C120 242 195 264 290 242 S478 240 640 274" fill="none" stroke="#f38b6c" stroke-opacity=".7" stroke-width="2"/>
                            <g transform="translate(44 42)">
                                <circle cx="28" cy="28" r="27" fill="#ea4e2d"/>
                                <path d="M28 13v16l10 7" fill="none" stroke="#fff5ea" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="28" cy="28" r="19" fill="none" stroke="#fff5ea" stroke-opacity=".75" stroke-width="1.5"/>
                                <text x="72" y="22" fill="#ffd9c9" font-family="Montserrat, sans-serif" font-size="13" font-weight="700" letter-spacing="1.5">JIHAD DHUHA</text>
                                <text x="72" y="47" fill="#fff" font-family="Montserrat, sans-serif" font-size="25" font-weight="800" letter-spacing="-.8">09.00–10.00</text>
                            </g>
                            <g fill="#fff5ea" font-family="Montserrat, sans-serif">
                                <text x="310" y="188" font-size="22" font-weight="800" letter-spacing="3" text-anchor="middle">GRATITUDE</text>
                                <text x="310" y="216" fill="#ffe0c7" font-size="11" font-weight="600" letter-spacing="2.6" text-anchor="middle">A QUIET START. A CLEAR DAY.</text>
                            </g>
                            <g transform="translate(44 346)">
                                <path d="M0 0h552" stroke="#fff5ea" stroke-opacity=".2"/>
                                <text x="0" y="30" fill="#ffb39b" font-family="Montserrat, sans-serif" font-size="13" font-weight="700" letter-spacing="1.8">WINNING THE DAY</text>
                                <text x="552" y="30" fill="#fff5ea" font-family="Montserrat, sans-serif" font-size="13" font-weight="600" text-anchor="end" letter-spacing="1.2">WITH GRATITUDE</text>
                            </g>
                        </svg>
                        <figcaption>Ritme pagi untuk menata niat sebelum ikhtiar.</figcaption>
                    </figure>
                    <div class="way-jihad"><span>{{ $content['dk_way']['jihad_label'] }}</span><strong>{{ $content['dk_way']['jihad_time'] }}</strong><p>{{ $content['dk_way']['jihad_description'] }}</p></div>
                </div>
            </div>
        </div>
    </section>

    <section class="way-section way-external" id="psikotes-tim" aria-labelledby="psychotest-title">
        <div class="container way-external-inner way-reveal">
            <div><p class="way-eyebrow">{{ $content['psychotest']['eyebrow'] }}</p><h2 id="psychotest-title">{{ $content['psychotest']['title'] }}</h2><p>{{ $content['psychotest']['description'] }}</p></div>
            <a class="way-button way-button-primary" href="{{ $content['psychotest']['url'] }}" target="_blank" rel="noopener noreferrer">{{ $content['psychotest']['action'] }} <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
        </div>
    </section>

    <section class="way-section way-secret" id="secret-potion" aria-labelledby="secret-title">
        <div class="container way-secret-inner way-reveal">
            <div><p class="way-eyebrow">{{ $content['secret_potion']['eyebrow'] }}</p><h2 id="secret-title">{{ $content['secret_potion']['title'] }}</h2><p>{{ $content['secret_potion']['description'] }}</p></div>
            <a class="way-secret-link" href="{{ $content['secret_potion']['url'] }}" target="_blank" rel="noopener noreferrer">{{ $content['secret_potion']['action'] }} <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
        </div>
    </section>
</main>
@endsection

@push('scripts')
    @vite('resources/js/dinoyo-way.js')
@endpush

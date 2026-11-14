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
                        <svg viewBox="0 0 520 300" role="img" aria-labelledby="gratitude-art-title gratitude-art-desc">
                            <title id="gratitude-art-title">Winning the day with gratitude</title>
                            <desc id="gratitude-art-desc">Matahari pagi terbit di atas horizon dengan lingkaran syukur dan penanda Jihad Dhuha.</desc>
                            <defs>
                                <linearGradient id="gratitude-sky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#263b45"/><stop offset="1" stop-color="#17272f"/></linearGradient>
                                <radialGradient id="gratitude-sun"><stop stop-color="#ffd6a3"/><stop offset=".45" stop-color="#ff9c83"/><stop offset="1" stop-color="#ea4e2d" stop-opacity="0"/></radialGradient>
                            </defs>
                            <rect width="520" height="300" rx="18" fill="url(#gratitude-sky)"/>
                            <circle cx="260" cy="126" r="96" fill="url(#gratitude-sun)" opacity=".85"/>
                            <circle cx="260" cy="126" r="31" fill="#ffd6a3"/>
                            <path d="M50 230 Q140 172 220 220 T390 205 T500 224 V300 H50Z" fill="#10191e"/>
                            <path d="M60 239 Q150 186 230 229 T390 215 T490 235" fill="none" stroke="#ff9c83" stroke-opacity=".55" stroke-width="2"/>
                            <circle cx="260" cy="126" r="72" fill="none" stroke="#ffe0c7" stroke-opacity=".6" stroke-dasharray="2 10" stroke-width="2"/>
                            <g fill="#fff5ea" font-family="Montserrat, sans-serif" text-anchor="middle">
                                <text x="260" y="117" font-size="14" font-weight="700" letter-spacing="2">WINNING THE DAY</text>
                                <text x="260" y="139" font-size="11" letter-spacing="1.5">WITH GRATITUDE</text>
                            </g>
                            <g transform="translate(34 34)">
                                <rect width="132" height="42" rx="21" fill="#ea4e2d"/>
                                <text x="66" y="26" fill="#fff" font-family="Montserrat, sans-serif" font-size="11" font-weight="700" text-anchor="middle">JIHAD DHUHA</text>
                            </g>
                            <text x="486" y="275" fill="#ffcfb9" font-family="Montserrat, sans-serif" font-size="12" font-weight="700" text-anchor="end">09.00–10.00</text>
                        </svg>
                        <figcaption>Syukur membuka langkah. Ikhtiar menjaga arah.</figcaption>
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
    <script src="{{ \App\Helpers\PublicAssetHelper::url('js/dinoyo-way.js') }}" defer></script>
@endpush

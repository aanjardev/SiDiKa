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
                <div class="way-hero-actions">
                    <a href="{{ $content['hero']['action_url'] }}" class="way-button way-button-primary">{{ $content['hero']['action'] }} <i class="bi bi-arrow-down-right" aria-hidden="true"></i></a>
                    <a href="{{ $content['hero']['secondary_action_url'] }}" class="way-hero-secondary">{{ $content['hero']['secondary_action'] }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </div>
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
                    <img src="{{ $imageSlots['philosophy']['url'] }}" alt="{{ $content['philosophy']['image_alt'] }}" loading="lazy" width="736" height="920">
                    <figcaption>{{ $content['philosophy']['image_caption'] }}</figcaption>
                </figure>
                <div class="way-story-copy">
                    <p class="way-eyebrow">{{ $content['philosophy']['eyebrow'] }}</p>
                    <h2 id="philosophy-title">{{ $content['philosophy']['title'] }}</h2>
                    <p class="way-lead">{{ $content['philosophy']['lead'] }}</p>
                    <div class="way-philosophy-reading">
                        @foreach($content['philosophy']['paragraphs'] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="way-section way-mission" aria-labelledby="mission-title">
        <div class="container way-mission-grid">
            <div class="way-reveal">
                <p class="way-eyebrow">{{ $content['mission']['eyebrow'] }}</p>
                <h2 id="mission-title">{{ $content['mission']['title'] }}</h2>
                <p class="way-section-description">{{ $content['mission']['description'] }}</p>
                <p class="way-note">{{ $content['mission']['detail'] }}</p>
            </div>
            <div class="way-orbit way-reveal">
                <div class="way-orbit-rings" aria-hidden="true"></div>
                <div class="way-orbit-center">@include('partials.dinoyo-way-aperture')<span>{{ $content['mission']['center_label'] }}</span></div>
                <ul class="way-orbit-points">
                    @foreach($content['mission']['areas'] as $area)
                        <li><i class="bi bi-{{ ['person', 'house-heart', 'briefcase', 'moon-stars', 'flag'][$loop->index % 5] }}" aria-hidden="true"></i><span>{{ $area }}</span></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section class="way-section way-values" id="nilai" aria-labelledby="values-title">
        <div class="container">
            <header class="way-section-heading way-reveal">
                <p class="way-eyebrow">{{ $content['values']['eyebrow'] }}</p>
                <h2 id="values-title">{{ $content['values']['title'] }}</h2>
                <p>{{ $content['values']['description'] }}</p>
            </header>
            <div class="way-value-selector" data-value-selector aria-label="{{ $content['values']['title'] }}">
                @foreach($content['values']['items'] as $value)
                    <button type="button" id="way-value-tab-{{ $loop->index }}" data-value-tab="{{ $loop->index }}" aria-controls="way-value-panel-{{ $loop->index }}">
                        <span class="way-value-icon" aria-hidden="true"><i class="bi bi-{{ ['lightning-charge', 'moon-stars', 'shield-check', 'diagram-3', 'graph-up-arrow', 'award'][$loop->index % 6] }}"></i></span>
                        <span class="way-value-label">{{ $value['name'] }}</span>
                    </button>
                @endforeach
            </div>
            <div class="way-values-panels">
                @foreach($content['values']['items'] as $value)
                    <article class="way-value" id="way-value-panel-{{ $loop->index }}" data-value-panel="{{ $loop->index }}" aria-labelledby="way-value-tab-{{ $loop->index }}">
                        <div class="way-value-photo">
                            <img src="{{ $imageSlots['pillar_'.$loop->index]['url'] }}" alt="{{ $value['image_alt'] }}" loading="lazy" width="760" height="900">
                        </div>
                        <div class="way-value-copy">
                            <h3>{{ $value['name'] }}</h3>
                            <p class="way-value-title">{{ $value['title'] }}</p>
                            <p>{{ $value['description'] }}</p>
                            <div class="way-value-behavior">
                                <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                <div><span>{{ $content['values']['behavior_label'] }}</span><p>{{ $value['behavior'] }}</p></div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="way-section" id="cara-kerja" aria-labelledby="standards-title">
        <div class="container">
            <div class="way-editorial-grid way-standards-layout">
                <header class="way-reveal way-standards-intro">
                    <p class="way-eyebrow">{{ $content['standards']['eyebrow'] }}</p>
                    <h2 id="standards-title">{{ $content['standards']['title'] }}</h2>
                    <p class="way-section-description">{{ $content['standards']['description'] }}</p>
                    <figure class="way-quality-photo">
                        <img src="{{ $imageSlots['standards']['url'] }}" alt="{{ $content['standards']['image_alt'] }}" loading="lazy" width="1280" height="1280">
                    </figure>
                </header>
                <div class="way-reveal way-standards-content">
                    <dl class="way-standards">
                        @foreach($content['standards']['items'] as $standard)
                            <div>
                                <dt><i class="bi bi-{{ ['shield-check', 'clock', 'eye', 'clipboard-check', 'chat-heart', 'arrow-repeat'][$loop->index % 6] }}" aria-hidden="true"></i>{{ $standard['title'] }}</dt>
                                <dd>{{ $standard['description'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                    <p class="way-note">{{ $content['standards']['note'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="way-section way-daily" aria-labelledby="daily-title">
        <div class="container">
            <header class="way-section-heading way-reveal">
                <p class="way-eyebrow">{{ $content['daily']['eyebrow'] }}</p>
                <h2 id="daily-title">{{ $content['daily']['title'] }}</h2>
                <p class="way-section-description">{{ $content['daily']['description'] }}</p>
            </header>
            <ol class="way-timeline">
                @foreach($content['daily']['items'] as $step)
                    <li class="way-reveal">
                        <div class="way-step-marker" aria-hidden="true"><i class="bi bi-{{ ['sunrise', 'shop', 'camera', 'lock', 'journal-check'][$loop->index % 5] }}"></i><span>{{ sprintf('%02d', $loop->iteration) }}</span></div>
                        <div>
                            <p class="way-step-time">{{ $step['time'] }}</p>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['description'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="way-section way-performance" aria-labelledby="performance-title">
        <div class="container">
            <div class="way-performance-intro way-reveal">
                <header>
                    <p class="way-eyebrow">{{ $content['performance']['eyebrow'] }}</p>
                    <h2 id="performance-title">{{ $content['performance']['title'] }}</h2>
                </header>
                <div class="way-prose">
                    <p>{{ $content['performance']['paragraphs'][0] }}</p>
                </div>
            </div>
            <div class="way-principles way-reveal">
                <figure class="way-principles-art" aria-hidden="true">
                    <img src="{{ asset('mainIMG/way-balanced-culture.svg') }}" alt="" width="360" height="320" loading="lazy">
                </figure>
                <blockquote class="way-principles-copy">
                    @foreach($content['performance']['principles'] as $principle)
                        <p>{{ $principle }}</p>
                    @endforeach
                </blockquote>
            </div>
        </div>
    </section>

    <section class="way-section way-career" id="bertumbuh" aria-labelledby="career-title">
        <div class="container">
            <header class="way-section-heading way-reveal">
                <p class="way-eyebrow">{{ $content['career']['eyebrow'] }}</p>
                <h2 id="career-title">{{ $content['career']['title'] }}</h2>
                <p>{{ $content['career']['description'] }}</p>
            </header>
            <p class="way-proposal-label">{{ $content['career']['proposal_label'] }}</p>
            <ol class="way-career-list">
                @foreach($content['career']['items'] as $level)
                    <li class="way-reveal" style="--way-level: {{ $loop->index }}">
                        <div class="way-career-rung"><span>{{ $content['career']['level_label'] }} {{ $loop->iteration }}</span><i class="bi bi-arrow-up-right" aria-hidden="true"></i></div>
                        <h3>{{ $level['title'] }}</h3>
                        <p>{{ $level['description'] }}</p>
                    </li>
                @endforeach
            </ol>
            <p class="way-note way-career-note">{{ $content['career']['note'] }}</p>
        </div>
    </section>

    <section class="way-section way-evaluation" aria-labelledby="evaluation-title">
        <div class="container way-editorial-grid">
            <header class="way-reveal">
                <p class="way-eyebrow">{{ $content['evaluation']['eyebrow'] }}</p>
                <h2 id="evaluation-title">{{ $content['evaluation']['title'] }}</h2>
                <p class="way-section-description">{{ $content['evaluation']['description'] }}</p>
                <figure class="way-evaluation-chart">
                    <svg viewBox="0 0 220 220" aria-hidden="true">
                        <circle class="way-chart-track" cx="110" cy="110" r="82" fill="none" stroke-width="24"/>
                        @php $chartOffset = 0; $chartTotal = 0; @endphp
                        @foreach($content['evaluation']['items'] as $factor)
                            <circle class="way-chart-segment way-chart-segment-{{ $loop->index }}" cx="110" cy="110" r="82" fill="none" stroke-width="24" pathLength="100" stroke-dasharray="{{ $factor['percentage'] }} {{ 100 - $factor['percentage'] }}" stroke-dashoffset="{{ -$chartOffset }}" transform="rotate(-90 110 110)"/>
                            @php $chartOffset += $factor['percentage']; $chartTotal += $factor['percentage']; @endphp
                        @endforeach
                        <text class="way-chart-total" x="110" y="117" text-anchor="middle">{{ $chartTotal }}<tspan>%</tspan></text>
                    </svg>
                    <figcaption>{{ $content['evaluation']['chart_label'] }}</figcaption>
                </figure>
            </header>
            <div class="way-reveal">
                <p class="way-proposal-label">{{ $content['evaluation']['proposal_label'] }}</p>
                <dl class="way-evaluation-list">
                    @foreach($content['evaluation']['items'] as $factor)
                        <div>
                            <dt><span class="way-chart-key way-chart-key-{{ $loop->index }}" aria-hidden="true"></span>{{ $factor['title'] }}</dt>
                            <dd>{{ $factor['percentage'] }}<span>%</span></dd>
                        </div>
                    @endforeach
                </dl>
                <p class="way-note">{{ $content['evaluation']['note'] }}</p>
                <p class="way-note">{{ $content['evaluation']['fairness_note'] }}</p>
            </div>
        </div>
    </section>

    <section class="way-section way-pledge" id="janji" aria-labelledby="pledge-title">
        <div class="container way-pledge-inner way-reveal">
            <div>
                <p class="way-eyebrow">{{ $content['pledge']['eyebrow'] }}</p>
                <h2 id="pledge-title">{{ $content['pledge']['title'] }}</h2>
                <div class="way-pledge-seal" aria-hidden="true">@include('partials.dinoyo-way-aperture')<span>{{ $content['pledge']['emblem_label'] }}</span></div>
            </div>
            <div>
                <ul class="way-pledge-lines">
                    @foreach($content['pledge']['lines'] as $line)
                        <li><i class="bi bi-check2" aria-hidden="true"></i>{{ $line }}</li>
                    @endforeach
                </ul>
                <p class="way-pledge-closing">{{ $content['pledge']['closing'] }}</p>
                <p class="way-salutation">{{ $content['pledge']['salutation'] }}</p>
            </div>
        </div>
    </section>

    <section class="way-section way-implementation" aria-labelledby="implementation-title">
        <div class="container">
            <header class="way-section-heading way-reveal">
                <p class="way-eyebrow">{{ $content['implementation']['eyebrow'] }}</p>
                <h2 id="implementation-title">{{ $content['implementation']['title'] }}</h2>
                <p>{{ $content['implementation']['description'] }}</p>
            </header>
            <p class="way-proposal-label">{{ $content['implementation']['proposal_label'] }}</p>
            <ol class="way-implementation-list">
                @foreach($content['implementation']['items'] as $step)
                    <li class="way-reveal">
                        <div class="way-implementation-marker" aria-hidden="true"><i class="bi bi-{{ ['megaphone', 'person-plus', 'clipboard-check', 'stars'][$loop->index % 4] }}"></i><span>{{ sprintf('%02d', $loop->iteration) }}</span></div>
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['description'] }}</p>
                    </li>
                @endforeach
            </ol>
            <p class="way-note way-implementation-note">{{ $content['implementation']['note'] }}</p>
        </div>
    </section>

    <section class="way-closing" aria-labelledby="closing-title">
        <div class="container way-closing-inner way-reveal">
            <div>
                <h2 id="closing-title">{{ $content['closing']['title'] }}</h2>
                <p>{{ $content['closing']['description'] }}</p>
            </div>
            <div class="way-closing-actions">
                <a href="{{ $content['closing']['action_url'] }}" class="way-closing-link way-closing-link-primary">{{ $content['closing']['action'] }} <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                <a href="{{ $content['closing']['secondary_action_url'] }}" class="way-closing-link way-closing-link-secondary">{{ $content['closing']['secondary_action'] }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
    <script src="{{ asset('js/dinoyo-way.js') }}" defer></script>
@endpush

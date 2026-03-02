@props(['settings' => []])

<section class="hero-section-1 position-relative bg-cover bg-center d-flex align-items-center justify-content-center" style="min-height: 500px; background-image: url('{{ $settings['background_image'] ?? '' }}'); background-size: cover; background-position: center;">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"></div>
    <div class="position-relative container text-center text-white" style="z-index: 10;">
        @if(!empty($settings['subtitle']))
            <p class="fs-5 fs-md-4 mb-3 opacity-75">{{ $settings['subtitle'] }}</p>
        @endif

        @if(!empty($settings['title']))
            <h1 class="display-3 display-md-1 fw-bold mb-4">{{ $settings['title'] }}</h1>
        @endif

        @if(!empty($settings['description']))
            <p class="fs-5 fs-md-4 mb-5 mx-auto" style="max-width: 900px;">{{ $settings['description'] }}</p>
        @endif

        @if(!empty($settings['button_text']) && !empty($settings['button_link']))
            <a href="{{ $settings['button_link'] }}" class="btn btn-primary btn-lg px-5 py-3 fw-semibold">
                {{ $settings['button_text'] }}
            </a>
        @endif
    </div>
</section>

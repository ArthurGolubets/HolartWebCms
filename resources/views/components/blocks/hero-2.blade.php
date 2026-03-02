@props(['settings' => []])

<section class="hero-section-2 position-relative d-flex align-items-center justify-content-center overflow-hidden" style="min-height: 500px;">
    @if(!empty($settings['video_url']))
        <video autoplay muted loop class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;">
            <source src="{{ $settings['video_url'] }}" type="video/mp4">
        </video>
    @endif

    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"></div>

    <div class="position-relative container text-center text-white" style="z-index: 10;">
        @if(!empty($settings['title']))
            <h1 class="display-3 display-md-1 fw-bold mb-4">{{ $settings['title'] }}</h1>
        @endif

        @if(!empty($settings['description']))
            <p class="fs-5 fs-md-4 mx-auto" style="max-width: 900px;">{{ $settings['description'] }}</p>
        @endif
    </div>
</section>

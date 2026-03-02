@props(['settings' => []])

@php
    $items = $settings['items'] ?? [];
@endphp

<section class="features-block py-5 bg-light">
    <div class="container">
        @if(!empty($settings['title']))
            <h2 class="display-6 fw-bold mb-5 text-center">{{ $settings['title'] }}</h2>
        @endif

        <div class="row g-5">
            @foreach($items as $item)
                <div class="col-md-6 col-lg-4">
                    <div class="feature-item text-center p-4">
                        @if(!empty($item['icon']))
                            <div class="display-3 mb-3">{{ $item['icon'] }}</div>
                        @endif
                        @if(!empty($item['title']))
                            <h3 class="h5 fw-semibold mb-3">{{ $item['title'] }}</h3>
                        @endif
                        @if(!empty($item['description']))
                            <p class="text-muted">{{ $item['description'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

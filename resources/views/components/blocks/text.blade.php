@props(['settings' => []])

<section class="text-block py-5">
    <div class="container">
        @if(!empty($settings['title']))
            <h2 class="display-6 fw-bold mb-4">{{ $settings['title'] }}</h2>
        @endif

        @if(!empty($settings['content']))
            <div class="prose">
                {!! nl2br(e($settings['content'])) !!}
            </div>
        @endif
    </div>
</section>

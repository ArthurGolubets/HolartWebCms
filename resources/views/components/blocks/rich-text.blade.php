@props(['settings' => []])

<section class="rich-text-block py-5">
    <div class="container">
        @if(!empty($settings['title']))
            <h2 class="display-6 fw-bold mb-4">{{ $settings['title'] }}</h2>
        @endif

        @if(!empty($settings['content']))
            <div class="prose">
                {!! $settings['content'] !!}
            </div>
        @endif
    </div>
</section>

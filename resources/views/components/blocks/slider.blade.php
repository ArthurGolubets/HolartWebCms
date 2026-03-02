@props(['settings' => []])

@php
    $slides = $settings['slides'] ?? [];
    $sliderId = 'slider-' . uniqid();
@endphp

<section class="slider-block py-5">
    <div class="container">
        <div id="{{ $sliderId }}" class="position-relative overflow-hidden rounded">
            <div class="slider-wrapper d-flex" style="transition: transform 0.5s ease;">
                @foreach($slides as $index => $slide)
                    <div class="slide position-relative" style="min-width: 100%;">
                        @if(!empty($slide['image']))
                            <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] ?? '' }}" class="w-100" style="height: 400px; object-fit: cover;">
                        @endif
                        @if(!empty($slide['title']) || !empty($slide['description']))
                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
                                <div class="text-center text-white px-4">
                                    @if(!empty($slide['title']))
                                        <h3 class="display-6 fw-bold mb-3">{{ $slide['title'] }}</h3>
                                    @endif
                                    @if(!empty($slide['description']))
                                        <p class="fs-5">{{ $slide['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if(count($slides) > 1)
                <!-- Navigation Arrows -->
                <button class="slider-prev position-absolute start-0 top-50 translate-middle-y btn btn-light bg-opacity-50 rounded-circle p-2 ms-3" style="z-index: 10;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button class="slider-next position-absolute end-0 top-50 translate-middle-y btn btn-light bg-opacity-50 rounded-circle p-2 me-3" style="z-index: 10;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <!-- Dots -->
                <div class="position-absolute bottom-0 start-50 translate-middle-x d-flex gap-2 mb-3">
                    @foreach($slides as $index => $slide)
                        <button class="slider-dot rounded-circle bg-white {{ $index === 0 ? 'bg-opacity-100' : 'bg-opacity-50' }}" style="width: 12px; height: 12px; border: none;"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Simple slider implementation (you can replace with Swiper.js or other library)
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.getElementById('{{ $sliderId }}');
        if (!slider) return;

        const wrapper = slider.querySelector('.slider-wrapper');
        const slides = slider.querySelectorAll('.slide');
        const prevBtn = slider.querySelector('.slider-prev');
        const nextBtn = slider.querySelector('.slider-next');
        const dots = slider.querySelectorAll('.slider-dot');

        let currentIndex = 0;

        function goToSlide(index) {
            currentIndex = index;
            wrapper.style.transform = `translateX(-${currentIndex * 100}%)`;
            dots.forEach((dot, i) => {
                dot.classList.toggle('bg-opacity-100', i === currentIndex);
                dot.classList.toggle('bg-opacity-50', i !== currentIndex);
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                goToSlide(currentIndex);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % slides.length;
                goToSlide(currentIndex);
            });
        }

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => goToSlide(index));
        });

        // Auto-play (optional)
        setInterval(() => {
            currentIndex = (currentIndex + 1) % slides.length;
            goToSlide(currentIndex);
        }, 5000);
    });
</script>
@endpush

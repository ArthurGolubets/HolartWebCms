@props(['settings' => []])

@php
    $catalogId = $settings['catalog_id'] ?? null;
    $limit = $settings['limit'] ?? 8;
    $columns = $settings['columns'] ?? '4';

    $products = [];
    if (class_exists(\App\Models\TProduct::class)) {
        $query = \App\Models\TProduct::query();
        if ($catalogId) {
            $query->where('catalog_id', $catalogId);
        }
        $products = $query->limit($limit)->get();
    }

    $colClass = match($columns) {
        '2' => 'col-md-6',
        '3' => 'col-md-6 col-lg-4',
        '4' => 'col-md-6 col-lg-3',
        default => 'col-md-3',
    };
@endphp

<section class="products-block py-5 bg-light">
    <div class="container">
        @if(!empty($settings['title']))
            <h2 class="display-6 fw-bold mb-5">{{ $settings['title'] }}</h2>
        @endif

        <div class="row g-4">
            @foreach($products as $product)
                <div class="{{ $colClass }}">
                    <div class="card h-100 shadow overflow-hidden">
                        @if($product->image)
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h5 fw-semibold mb-2">{{ $product->name }}</h3>
                            @if($product->price)
                                <p class="fs-4 fw-bold text-primary mb-3">{{ number_format($product->price, 0, ',', ' ') }} ₽</p>
                            @endif
                            <a href="/products/{{ $product->slug }}" class="btn btn-primary w-100 mt-auto">
                                Подробнее
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

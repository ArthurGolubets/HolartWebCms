@props(['settings' => []])

@php
    $dataSource = $settings['data_source'] ?? 'manual';
    $limit = $settings['limit'] ?? 6;
    $columns = $settings['columns'] ?? '3';
    $items = [];

    if ($dataSource === 'products' && class_exists(\App\Models\TProduct::class)) {
        $items = \App\Models\TProduct::limit($limit)
            ->get()
            ->map(fn($p) => [
                'title' => $p->name,
                'description' => $p->description,
                'image' => $p->image,
                'link' => '/products/' . $p->slug,
            ]);
    } elseif ($dataSource === 'infoblocks' && class_exists(\App\Models\TInfoBlockElement::class)) {
        $sourceId = $settings['source_id'] ?? null;
        $query = \App\Models\TInfoBlockElement::query();
        if ($sourceId) {
            $query->where('info_block_id', $sourceId);
        }
        $items = $query->limit($limit)
            ->get()
            ->map(fn($e) => [
                'title' => $e->name,
                'description' => $e->preview_text ?? '',
                'image' => $e->preview_picture ?? '',
                'link' => '#',
            ]);
    }

    $colClass = match($columns) {
        '2' => 'col-md-6',
        '3' => 'col-md-6 col-lg-4',
        '4' => 'col-md-6 col-lg-3',
        default => 'col-md-4',
    };
@endphp

<section class="cards-block py-5">
    <div class="container">
        @if(!empty($settings['title']))
            <h2 class="display-6 fw-bold mb-5 text-center">{{ $settings['title'] }}</h2>
        @endif

        <div class="row g-4">
            @foreach($items as $item)
                <div class="{{ $colClass }}">
                    <div class="card h-100 shadow overflow-hidden">
                        @if(!empty($item['image']))
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h3 class="card-title h5 fw-semibold mb-2">{{ $item['title'] }}</h3>
                            @if(!empty($item['description']))
                                <p class="card-text text-muted mb-3">{{ Str::limit($item['description'], 100) }}</p>
                            @endif
                            <a href="{{ $item['link'] }}" class="text-primary text-decoration-none fw-medium">
                                Подробнее →
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

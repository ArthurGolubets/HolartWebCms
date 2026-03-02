@props(['settings' => []])

@php
    $parentId = $settings['parent_id'] ?? null;
    $columns = $settings['columns'] ?? '3';

    $catalogs = [];
    if (class_exists(\App\Models\TCatalog::class)) {
        $query = \App\Models\TCatalog::query();
        if ($parentId) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }
        $catalogs = $query->orderBy('sort')->get();
    }

    $colClass = match($columns) {
        '2' => 'col-md-6',
        '3' => 'col-md-6 col-lg-4',
        '4' => 'col-md-6 col-lg-3',
        default => 'col-md-4',
    };
@endphp

<section class="catalogs-block py-5">
    <div class="container">
        @if(!empty($settings['title']))
            <h2 class="display-6 fw-bold mb-5">{{ $settings['title'] }}</h2>
        @endif

        <div class="row g-4">
            @foreach($catalogs as $catalog)
                <div class="{{ $colClass }}">
                    <a href="/catalogs/{{ $catalog->slug }}" class="catalog-card card h-100 shadow text-decoration-none overflow-hidden">
                        @if($catalog->image)
                            <img src="{{ $catalog->image }}" alt="{{ $catalog->name }}" class="card-img-top" style="height: 200px; object-fit: cover; transition: transform 0.3s;">
                        @endif
                        <div class="card-body">
                            <h3 class="card-title h5 fw-semibold mb-2 text-body">{{ $catalog->name }}</h3>
                            @if($catalog->description)
                                <p class="card-text text-muted small">{{ Str::limit($catalog->description, 80) }}</p>
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

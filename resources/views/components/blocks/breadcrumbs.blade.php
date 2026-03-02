@props(['settings' => []])

<nav class="breadcrumbs py-3 bg-light" aria-label="breadcrumb">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="/" class="text-primary text-decoration-none">Главная</a>
            </li>
            @if(isset($breadcrumbs))
                @foreach($breadcrumbs as $breadcrumb)
                    @if(!empty($breadcrumb['link']))
                        <li class="breadcrumb-item">
                            <a href="{{ $breadcrumb['link'] }}" class="text-primary text-decoration-none">
                                {{ $breadcrumb['title'] }}
                            </a>
                        </li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['title'] }}</li>
                    @endif
                @endforeach
            @endif
        </ol>
    </div>
</nav>

{{-- Header Template 1: Classic centered logo with menu below --}}
@php
    use HolartWeb\HolartCMS\Models\TPanelSettings;
    use HolartWeb\HolartCMS\Models\Menus\TMenu;

    $logoPath = TPanelSettings::get('logo_path');
    $siteName = TPanelSettings::get('site_name', 'HolartCMS');
    $logoWidth = TPanelSettings::get('logo_width');

    // Get header1 settings
    $headerSettings = TPanelSettings::get('header_template_settings', []);
    if ($headerSettings && $headerSettings !== []) $headerSettings = json_decode($headerSettings, 1);
    $header1Settings = is_array($headerSettings) && isset($headerSettings['header1']) ? $headerSettings['header1'] : [];

    $headerMenuId = $header1Settings['menu_id'] ?? null;
    $headerMenu = $headerMenuId ? TMenu::with(['rootItems' => function ($query) {
        $query->where('is_active', true)
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->orderBy('sort');
            }]);
    }])->where('id', $headerMenuId)->where('is_active', true)->first() : null;

    $bgColor = $header1Settings['bg_color'] ?? '#ffffff';
    $textColor = $header1Settings['text_color'] ?? '#495057';
    $linkColor = $header1Settings['link_color'] ?? '#495057';
    $linkHoverColor = $header1Settings['link_hover_color'] ?? '#0d6efd';
@endphp

<header class="header-template-1 shadow-sm sticky-top" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
    <div class="container">
        <!-- Logo Row -->
        <div class="logo-row text-center py-4">
            <a href="/" class="logo d-inline-block">
                @if($logoPath)
                    <img
                        src="{{ asset($logoPath) }}"
                        alt="{{ $siteName }}"
                        class="logo-img"
                        style="max-height: 60px; height: auto; @if($logoWidth) width: {{ $logoWidth }}px; @endif"
                    />
                @else
                    <span class="logo-text">{{ $siteName }}</span>
                @endif
            </a>
        </div>

        <!-- Navigation Row -->
        @if($headerMenu && $headerMenu->rootItems->count() > 0)
        <nav class="navbar navbar-expand-lg navbar-light border-top border-bottom" style="background-color: rgba(0,0,0,0.02);">
            <div class="container-fluid justify-content-center">
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mainNav"
                    aria-controls="mainNav"
                    aria-expanded="false"
                    aria-label="Открыть меню"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-center" id="mainNav">
                    <ul class="navbar-nav">
                        @foreach($headerMenu->rootItems as $item)
                            @if($item->children && $item->children->count() > 0)
                                <li class="nav-item dropdown">
                                    <a
                                        class="nav-link dropdown-toggle px-3"
                                        href="#"
                                        role="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        {{ $item->title }}
                                    </a>
                                    <ul class="dropdown-menu shadow-sm border-0">
                                        @foreach($item->children as $child)
                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="{{ $child->url }}"
                                                    @if($child->target === '_blank') target="_blank" rel="noopener noreferrer" @endif
                                                >
                                                    {{ $child->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a
                                        class="nav-link px-3"
                                        href="{{ $item->url }}"
                                        @if($item->target === '_blank') target="_blank" rel="noopener noreferrer" @endif
                                    >
                                        {{ $item->title }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </nav>
        @endif
    </div>
</header>

<style>
.header-template-1 .logo {
    text-decoration: none;
    transition: opacity 0.3s ease;
}

.header-template-1 .logo:hover {
    opacity: 0.8;
}

.header-template-1 .logo-img {
    max-width: 100%;
    object-fit: contain;
}

.header-template-1 .logo-text {
    font-size: 2rem;
    font-weight: 700;
    color: #212529;
    letter-spacing: -0.5px;
}

.header-template-1 .navbar {
    padding: 0.75rem 0;
}

.header-template-1 .nav-link {
    color: {{ $linkColor }};
    font-weight: 500;
    font-size: 0.95rem;
    position: relative;
    transition: color 0.3s ease;
}

.header-template-1 .nav-link:hover,
.header-template-1 .nav-link:focus {
    color: {{ $linkHoverColor }};
}

.header-template-1 .nav-link::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: {{ $linkHoverColor }};
    transition: width 0.3s ease;
}

.header-template-1 .nav-link:hover::after {
    width: 80%;
}

.header-template-1 .dropdown-menu {
    margin-top: 0.5rem;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.header-template-1 .dropdown-item {
    padding: 0.5rem 1.5rem;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.header-template-1 .dropdown-item:hover {
    background-color: #f8f9fa;
    color: {{ $linkHoverColor }};
    padding-left: 2rem;
}

@media (max-width: 991px) {
    .header-template-1 .logo-text {
        font-size: 1.5rem;
    }

    .header-template-1 .navbar-collapse {
        margin-top: 1rem;
        text-align: center;
    }

    .header-template-1 .navbar-nav {
        width: 100%;
    }

    .header-template-1 .nav-link::after {
        display: none;
    }

    .header-template-1 .dropdown-menu {
        text-align: center;
        border: none;
        background: #f8f9fa;
    }

    .header-template-1 .dropdown-item:hover {
        padding-left: 1.5rem;
    }
}
</style>

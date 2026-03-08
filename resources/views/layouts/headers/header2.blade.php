{{-- Header Template 2: Logo left, menu right (horizontal) --}}
@php
    use HolartWeb\HolartCMS\Models\TPanelSettings;
    use HolartWeb\HolartCMS\Models\Menus\TMenu;

    $logoPath = TPanelSettings::get('logo_path');
    $siteName = TPanelSettings::get('site_name', 'HolartCMS');
    $logoWidth = TPanelSettings::get('logo_width');

    // Get header2 settings
    $headerSettings = TPanelSettings::get('header_template_settings', []);
    if ($headerSettings && $headerSettings !== []) $headerSettings = json_decode($headerSettings, 1);
    $header2Settings = is_array($headerSettings) && isset($headerSettings['header2']) ? $headerSettings['header2'] : [];

    $headerMenuId = $header2Settings['menu_id'] ?? null;
    $headerMenu = $headerMenuId ? TMenu::with(['rootItems' => function ($query) {
        $query->where('is_active', true)
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->orderBy('sort');
            }]);
    }])->where('id', $headerMenuId)->where('is_active', true)->first() : null;

    $bgColor = $header2Settings['bg_color'] ?? '#ffffff';
    $textColor = $header2Settings['text_color'] ?? '#495057';
    $linkColor = $header2Settings['link_color'] ?? '#495057';
    $linkHoverColor = $header2Settings['link_hover_color'] ?? '#0d6efd';
    $buttonColor = $header2Settings['button_color'] ?? '#0d6efd';
    $showCart = $header2Settings['show_cart'] ?? false;
    $showAccount = $header2Settings['show_account'] ?? false;
@endphp

<header class="header-template-2 sticky-top" style="background-color: {{ $bgColor }};">
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
        <div class="container">
            <!-- Logo -->
            <a href="/" class="navbar-brand" style="color: {{ $textColor }};">
                @if($logoPath)
                    <img
                        src="{{ asset('storage/' . $logoPath) }}"
                        alt="{{ $siteName }}"
                        class="logo-img"
                        style="max-height: 50px; height: auto; @if($logoWidth) width: {{ $logoWidth }}px; @endif"
                    />
                @else
                    <span class="logo-text">{{ $siteName }}</span>
                @endif
            </a>

            <!-- Mobile Toggle -->
            <button
                class="navbar-toggler border-0"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNav"
                aria-controls="mainNav"
                aria-expanded="false"
                aria-label="Открыть меню"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation -->
            <div class="collapse navbar-collapse" id="mainNav">
                @if($headerMenu && $headerMenu->rootItems->count() > 0)
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        @foreach($headerMenu->rootItems as $item)
                            @if($item->children && $item->children->count() > 0)
                                <li class="nav-item dropdown">
                                    <a
                                        class="nav-link dropdown-toggle px-lg-3"
                                        href="#"
                                        role="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        style="color: {{ $linkColor }};"
                                    >
                                        {{ $item->title }}
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
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
                                        class="nav-link px-lg-3"
                                        href="{{ $item->url }}"
                                        style="color: {{ $linkColor }};"
                                        @if($item->target === '_blank') target="_blank" rel="noopener noreferrer" @endif
                                    >
                                        {{ $item->title }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif

                <!-- Action Buttons -->
                @if($showCart || $showAccount)
                    <div class="d-flex gap-2 ms-3">
                        @if($showCart)
                            <a href="/cart" class="btn btn-sm d-flex align-items-center gap-1" style="background-color: {{ $buttonColor }}; color: white; border-color: {{ $buttonColor }};">
                                <svg class="w-4 h-4" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                </svg>
                                <span class="d-none d-md-inline">Корзина</span>
                            </a>
                        @endif
                        @if($showAccount)
                            <a href="/account" class="btn btn-sm d-flex align-items-center gap-1" style="background-color: {{ $buttonColor }}; color: white; border-color: {{ $buttonColor }};">
                                <svg class="w-4 h-4" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                </svg>
                                <span class="d-none d-md-inline">Аккаунт</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </nav>
</header>

<style>
    .header-template-2 .navbar {
        padding: 1rem 0;
        transition: all 0.3s ease;
    }

    .header-template-2 .navbar-brand {
        font-size: 1.5rem;
        font-weight: 700;
        text-decoration: none;
        transition: opacity 0.3s ease;
    }

    .header-template-2 .navbar-brand:hover {
        opacity: 0.8;
    }

    .header-template-2 .logo-img {
        max-width: 100%;
        object-fit: contain;
    }

    .header-template-2 .logo-text {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .header-template-2 .nav-link {
        font-weight: 500;
        font-size: 0.95rem;
        position: relative;
        transition: color 0.3s ease;
    }

    .header-template-2 .nav-link:hover,
    .header-template-2 .nav-link:focus {
        color: {{ $linkHoverColor }} !important;
    }

    .header-template-2 .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: {{ $linkHoverColor }};
        transition: width 0.3s ease;
    }

    .header-template-2 .nav-link:hover::after {
        width: 60%;
    }

    .header-template-2 .dropdown-menu {
        margin-top: 0.5rem;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        min-width: 200px;
    }

    .header-template-2 .dropdown-item {
        padding: 0.6rem 1.5rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .header-template-2 .dropdown-item:hover {
        background-color: #f8f9fa;
        color: {{ $linkHoverColor }};
        padding-left: 2rem;
    }

    .header-template-2 .btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    @media (max-width: 991px) {
        .header-template-2 .navbar-collapse {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0,0,0,0.1);
        }

        .header-template-2 .navbar-nav {
            width: 100%;
        }

        .header-template-2 .nav-item {
            text-align: center;
            padding: 0.25rem 0;
        }

        .header-template-2 .nav-link::after {
            display: none;
        }

        .header-template-2 .dropdown-menu {
            text-align: center;
            border: none;
            background: #f8f9fa;
        }

        .header-template-2 .dropdown-item:hover {
            padding-left: 1.5rem;
        }

        .header-template-2 .d-flex.gap-2 {
            justify-content: center;
            margin-top: 1rem;
        }
    }
</style>

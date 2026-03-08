{{-- Footer Template 1: Simple centered footer --}}
@php
    use HolartWeb\HolartCMS\Models\TPanelSettings;
    use HolartWeb\HolartCMS\Models\Menus\TMenu;

    $logoPath = TPanelSettings::get('logo_path');
    $siteName = TPanelSettings::get('site_name', 'HolartCMS');

    // Get footer1 settings
    $footerSettings = TPanelSettings::get('footer_template_settings', []);
    if ($footerSettings && $footerSettings !== []) $footerSettings = json_decode($footerSettings, 1);
    $footer1Settings = is_array($footerSettings) && isset($footerSettings['footer1']) ? $footerSettings['footer1'] : [];

    $footerMenuId = $footer1Settings['menu_id'] ?? null;
    $footerMenu = $footerMenuId ? TMenu::with(['rootItems' => function ($query) {
        $query->where('is_active', true)
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->orderBy('sort');
            }]);
    }])->where('id', $footerMenuId)->where('is_active', true)->first() : null;

    $bgColor = $footer1Settings['bg_color'] ?? '#f8f9fa';
    $textColor = $footer1Settings['text_color'] ?? '#6c757d';
    $linkColor = $footer1Settings['link_color'] ?? '#6c757d';
    $linkHoverColor = $footer1Settings['link_hover_color'] ?? '#0d6efd';
@endphp

<footer class="footer-template-1 py-5 mt-auto border-top" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
    <div class="container">
        <!-- Footer Menu -->
        @if($footerMenu && $footerMenu->rootItems->count() > 0)
        <nav class="footer-nav text-center mb-4">
            <ul class="list-inline mb-0">
                @foreach($footerMenu->rootItems as $item)
                    <li class="list-inline-item px-2">
                        <a
                            href="{{ $item->url }}"
                            class="footer-link"
                            style="color: {{ $linkColor }};"
                            @if($item->target === '_blank') target="_blank" rel="noopener noreferrer" @endif
                        >
                            {{ $item->title }}
                        </a>
                    </li>
                    @if(!$loop->last)
                        <li class="list-inline-item" style="color: {{ $textColor }};">•</li>
                    @endif
                @endforeach
            </ul>
        </nav>
        @endif

        <!-- Logo/Brand -->
        @if($logoPath)
        <div class="text-center mb-3">
            <img
                src="{{ asset('storage/' . $logoPath) }}"
                alt="{{ $siteName }}"
                style="max-height: 40px; height: auto; opacity: 0.7;"
            />
        </div>
        @endif

        <!-- Copyright -->
        <div class="text-center">
            <p class="mb-0 small" style="color: {{ $textColor }};">
                © {{ date('Y') }} {{ $siteName }}. Все права защищены.
            </p>
        </div>
    </div>
</footer>

<style>
.footer-template-1 .footer-link {
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: color 0.3s ease;
}

.footer-template-1 .footer-link:hover {
    color: {{ $linkHoverColor }} !important;
}

@media (max-width: 767px) {
    .footer-template-1 .list-inline-item {
        display: block;
        margin: 0.5rem 0;
        padding: 0 !important;
    }

    .footer-template-1 .list-inline-item:not(:has(a)) {
        display: none;
    }
}
</style>

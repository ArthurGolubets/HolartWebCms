{{-- Footer Template 2: Multi-column footer --}}
@php
    use HolartWeb\HolartCMS\Models\TPanelSettings;
    use HolartWeb\HolartCMS\Models\Menus\TMenu;

    $logoPath = TPanelSettings::get('logo_path');
    $siteName = TPanelSettings::get('site_name', 'HolartCMS');
    $siteDescription = TPanelSettings::get('site_description');
    $contactEmail = TPanelSettings::get('contact_email');
    $contactPhone = TPanelSettings::get('contact_phone');

    // Get footer2 settings
    $footerSettings = TPanelSettings::get('footer_template_settings', []);
    if ($footerSettings && $footerSettings !== []) $footerSettings = json_decode($footerSettings, 1);
    $footer2Settings = is_array($footerSettings) && isset($footerSettings['footer2']) ? $footerSettings['footer2'] : [];

    $footerMenuId = $footer2Settings['menu_id'] ?? null;
    $footerMenu = $footerMenuId ? TMenu::with(['rootItems' => function ($query) {
        $query->where('is_active', true)
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->orderBy('sort');
            }]);
    }])->where('id', $footerMenuId)->where('is_active', true)->first() : null;

    $bgColor = $footer2Settings['bg_color'] ?? '#212529';
    $textColor = $footer2Settings['text_color'] ?? '#ffffff';
    $linkColor = $footer2Settings['link_color'] ?? '#adb5bd';
    $linkHoverColor = $footer2Settings['link_hover_color'] ?? '#ffffff';
@endphp

<footer class="footer-template-2 py-5 mt-auto" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
    <div class="container">
        <div class="row g-4">
            <!-- Logo & Description Column -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand mb-3">
                    @if($logoPath)
                        <img
                            src="{{ asset('storage/' . $logoPath) }}"
                            alt="{{ $siteName }}"
                            class="footer-logo mb-3"
                            style="max-height: 50px; height: auto; filter: brightness(0) invert(1);"
                        />
                    @else
                        <h5 class="mb-3 fw-bold">{{ $siteName }}</h5>
                    @endif
                </div>
                @if($siteDescription)
                    <p style="color: {{ $linkColor }};">{{ $siteDescription }}</p>
                @endif
            </div>

            <!-- Navigation Column -->
            <div class="col-lg-4 col-md-6">
                <h6 class="text-uppercase mb-3 fw-bold" style="color: {{ $textColor }};">Навигация</h6>
                @if($footerMenu && $footerMenu->rootItems->count() > 0)
                <ul class="list-unstyled footer-links">
                    @foreach($footerMenu->rootItems as $item)
                        <li class="mb-2">
                            <a
                                href="{{ $item->url }}"
                                class="text-decoration-none d-inline-block"
                                style="color: {{ $linkColor }};"
                                @if($item->target === '_blank') target="_blank" rel="noopener noreferrer" @endif
                            >
                                {{ $item->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                @endif
            </div>

            <!-- Contact Column -->
            <div class="col-lg-4 col-md-6">
                <h6 class="text-uppercase mb-3 fw-bold" style="color: {{ $textColor }};">Контакты</h6>
                <ul class="list-unstyled" style="color: {{ $linkColor }};">
                    @if($contactEmail)
                        <li class="mb-2">
                            <i class="bi bi-envelope me-2"></i>
                            <a href="mailto:{{ $contactEmail }}" class="text-decoration-none" style="color: {{ $linkColor }};">
                                {{ $contactEmail }}
                            </a>
                        </li>
                    @endif
                    @if($contactPhone)
                        <li class="mb-2">
                            <i class="bi bi-telephone me-2"></i>
                            <a href="tel:{{ $contactPhone }}" class="text-decoration-none" style="color: {{ $linkColor }};">
                                {{ $contactPhone }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <hr class="border-secondary my-4 opacity-25" />

        <!-- Copyright -->
        <div class="row">
            <div class="col-12 text-center">
                <p class="mb-0 small" style="color: {{ $linkColor }};">
                    © {{ date('Y') }} {{ $siteName }}. Все права защищены.
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
.footer-template-2 .footer-links a {
    transition: all 0.3s ease;
}

.footer-template-2 .footer-links a:hover {
    color: {{ $linkHoverColor }} !important;
    padding-left: 8px;
}

.footer-template-2 a:hover {
    color: {{ $linkHoverColor }} !important;
}

@media (max-width: 767px) {
    .footer-template-2 .col-lg-4,
    .footer-template-2 .col-md-6 {
        text-align: center;
    }

    .footer-template-2 .footer-links {
        padding-left: 0;
    }
}
</style>

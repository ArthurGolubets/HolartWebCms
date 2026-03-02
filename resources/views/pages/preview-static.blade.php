<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->meta_title ?? $page->title }}</title>
    @if($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
    @if($page->meta_keywords)
        <meta name="keywords" content="{{ $page->meta_keywords }}">
    @endif

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Preview Banner Style -->
    <style>
        .preview-banner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            z-index: 9999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .preview-banner-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .preview-banner-text {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .preview-banner-icon {
            width: 20px;
            height: 20px;
        }
        .preview-banner-close {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .preview-banner-close:hover {
            background: rgba(255,255,255,0.3);
        }
        body {
            padding-top: 60px;
        }
    </style>
</head>
<body>
    <!-- Preview Banner -->
    <div class="preview-banner" id="previewBanner">
        <div class="preview-banner-content">
            <div class="preview-banner-text">
                <svg class="preview-banner-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <strong>Режим предпросмотра</strong>
                <span style="opacity: 0.9;">{{ $page->title }}</span>
            </div>
            <button class="preview-banner-close" onclick="document.getElementById('previewBanner').remove(); document.body.style.paddingTop = '0';">
                Закрыть
            </button>
        </div>
    </div>

    <!-- Page Content -->
    <main>
        <div class="container py-5">
            <h1 class="mb-4">{{ $page->title }}</h1>
            <div class="content">
                {!! $page->content !!}
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

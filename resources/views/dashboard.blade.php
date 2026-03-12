<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('axora-cms.name', 'AxoraCMS') }}</title>
    @php
        $manifest = json_decode(file_get_contents(public_path('vendor/axora-cms/.vite/manifest.json')), true);
        $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
        $cssFile = $manifest['resources/js/app.js']['css'][0] ?? null;
    @endphp
    @if($cssFile)
        <link rel="stylesheet" href="{{ asset('vendor/axora-cms/' . $cssFile) }}">
    @endif
</head>
<body>
    <div id="app"></div>
    @if($jsFile)
        <script type="module" src="{{ asset('vendor/axora-cms/' . $jsFile) }}"></script>
    @endif
</body>
</html>

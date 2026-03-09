<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO Meta Tags - работают для страниц, каталогов и товаров --}}
    @if($pageData)
        <title>{{ $pageData['meta_title'] ?? config('app.name') }}</title>
        <meta name="description" content="{{ $pageData['meta_description'] ?? '' }}">
        <meta name="keywords" content="{{ $pageData['meta_keywords'] ?? '' }}">
    @else
        <title>{{ config('app.name') }}</title>
    @endif
</head>
<body>
    <div class="container">
        {{-- Проверка наличия данных страницы --}}
        @if($pageData)
            <article>
                {{-- Заголовок --}}
                <h1>{{ $pageData['title'] }}</h1>

                {{-- Контент (если есть) --}}
                @if(!empty($pageData['content']))
                    <div class="content">
                        {!! $pageData['content'] !!}
                    </div>
                @endif

                {{-- Информация о типе сущности --}}
                <div class="page-info">
                    <p>Тип: {{ $pageData['type'] }}</p>
                    <p>ID: {{ $pageData['id'] }}</p>
                    <p>Slug: {{ $pageData['slug'] }}</p>
                </div>

                {{-- Дополнительная логика в зависимости от типа --}}
                @switch($pageData['type'])
                    @case('page')
                        <p class="badge">Это страница из модуля SEO & Страницы</p>
                        @break

                    @case('catalog')
                        <p class="badge">Это каталог товаров</p>
                        {{-- Доступ к полной сущности --}}
                        @if(isset($pageData['entity']))
                            {{-- Например, можно получить товары каталога --}}
                            {{-- $pageData['entity']->products --}}
                        @endif
                        @break

                    @case('product')
                        <p class="badge">Это страница товара</p>
                        {{-- Доступ к полной сущности --}}
                        @if(isset($pageData['entity']))
                            {{-- Например, можно получить цену товара --}}
                            {{-- $pageData['entity']->price --}}
                        @endif
                        @break
                @endswitch
            </article>
        @else
            {{-- Страница не найдена в системе CMS --}}
            <h1>Добро пожаловать!</h1>
        @endif
    </div>
</body>
</html>

# PageDataService - Унифицированное получение данных страниц

## Описание

`PageDataService` автоматически определяет тип текущей страницы (обычная страница, каталог или товар) и предоставляет унифицированные SEO-данные во все Blade-шаблоны через переменную `$pageData`.

## Автоматическая работа

Сервис работает автоматически через middleware `SharePageData`, который добавлен в группу `web` middleware. При каждом запросе он:

1. Проверяет текущий роут
2. Ищет соответствующую страницу в модуле "Страницы и SEO"
3. Если не найдено, проверяет каталоги (если модуль установлен)
4. Если не найдено, проверяет товары (если модуль установлен)
5. Передает результат во все views через переменную `$pageData`

## Структура данных

Независимо от типа сущности (страница, каталог, товар), `$pageData` имеет одинаковую структуру:

```php
[
    'type' => 'page|catalog|product',  // Тип сущности
    'id' => 123,                        // ID записи
    'title' => 'Заголовок',             // Основной заголовок
    'meta_title' => 'SEO заголовок',    // Meta title для SEO
    'meta_description' => 'Описание',   // Meta description
    'meta_keywords' => 'ключевые слова', // Meta keywords
    'content' => '<p>HTML контент</p>', // Контент страницы/товара/каталога
    'slug' => 'url-slug',               // URL slug
    'entity' => $model,                 // Полная модель сущности (для доп. данных)
]
```

## Использование в Blade-шаблонах

### Базовое использование SEO-тегов

```blade
<head>
    @if($pageData)
        <title>{{ $pageData['meta_title'] ?? config('app.name') }}</title>
        <meta name="description" content="{{ $pageData['meta_description'] ?? '' }}">
        <meta name="keywords" content="{{ $pageData['meta_keywords'] ?? '' }}">
    @else
        <title>{{ config('app.name') }}</title>
    @endif
</head>
```

### Вывод контента

```blade
@if($pageData)
    <h1>{{ $pageData['title'] }}</h1>

    @if(!empty($pageData['content']))
        <div class="content">
            {!! $pageData['content'] !!}
        </div>
    @endif
@endif
```

### Условная логика по типу

```blade
@if($pageData)
    @switch($pageData['type'])
        @case('page')
            {{-- Специфичная логика для страниц --}}
            <div class="page-content">
                {!! $pageData['content'] !!}
            </div>
            @break

        @case('catalog')
            {{-- Специфичная логика для каталога --}}
            <div class="catalog">
                <h1>{{ $pageData['title'] }}</h1>
                {{-- Можно получить товары через entity --}}
                @foreach($pageData['entity']->products as $product)
                    <div class="product-card">{{ $product->name }}</div>
                @endforeach
            </div>
            @break

        @case('product')
            {{-- Специфичная логика для товара --}}
            <div class="product">
                <h1>{{ $pageData['title'] }}</h1>
                <div class="price">{{ $pageData['entity']->price }} ₽</div>
                <div class="description">{!! $pageData['content'] !!}</div>
            </div>
            @break
    @endswitch
@endif
```

### Доступ к полной модели

Если нужны дополнительные данные, недоступные в унифицированной структуре, используйте `entity`:

```blade
@if($pageData && $pageData['type'] === 'product')
    {{-- Доступ к специфичным полям товара --}}
    <div class="product-details">
        <p>Артикул: {{ $pageData['entity']->sku }}</p>
        <p>Цена: {{ $pageData['entity']->price }} ₽</p>

        @if($pageData['entity']->old_price)
            <p>Старая цена: {{ $pageData['entity']->old_price }} ₽</p>
        @endif

        @if($pageData['entity']->is_new)
            <span class="badge">Новинка</span>
        @endif
    </div>
@endif
```

## Поддерживаемые URL-паттерны

### Страницы (модуль SEO)
- По имени роута: любой роут с именем, совпадающим с `route_name` в таблице `t_pages`
- По slug: любой URL, совпадающий со slug страницы
- Главная страница: `/` → slug = `home`

### Каталоги
- Паттерн: `/catalog/{slug}`
- Пример: `/catalog/technika` → ищет каталог с slug = `technika`

### Товары
- Паттерн: `/product/{slug}`
- Пример: `/product/macbook-air-2pro` → ищет товар с slug = `macbook-air-2pro`

## Приоритет поиска

1. **Страницы** - сначала ищется в модуле "Страницы и SEO"
2. **Каталоги** - если не найдена страница, проверяется каталог
3. **Товары** - если не найден каталог, проверяется товар
4. **null** - если ничего не найдено, `$pageData` будет `null`

## Проверка установленных модулей

Сервис автоматически проверяет наличие таблиц в БД:
- `t_pages` - для модуля страниц
- `t_catalogs` - для модуля каталогов
- `t_products` - для модуля товаров

Если таблица не существует, соответствующий тип поиска пропускается.

## Производительность

- Сервис зарегистрирован как **singleton**, поэтому создается только один раз
- Использует **eager loading** для связанных данных
- Проверяет только активные записи (`is_active = true`)
- Пропускает admin-роуты автоматически

## Отключение для конкретных роутов

Если нужно отключить `SharePageData` middleware для определенных роутов:

```php
// В bootstrap/app.php или routes/web.php
Route::get('/custom-page', function() {
    return view('custom');
})->withoutMiddleware(\HolartWeb\AxoraCMS\Http\Middleware\SharePageData::class);
```

## Примеры типовых задач

### 1. Универсальный layout с SEO

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>{{ $pageData['meta_title'] ?? config('app.name') }}</title>
    <meta name="description" content="{{ $pageData['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $pageData['meta_keywords'] ?? '' }}">
</head>
<body>
    @yield('content')
</body>
</html>
```

### 2. Динамические хлебные крошки

```blade
@if($pageData)
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li><a href="/">Главная</a></li>

            @if($pageData['type'] === 'product' && $pageData['entity']->catalog)
                <li><a href="/catalog/{{ $pageData['entity']->catalog->slug }}">
                    {{ $pageData['entity']->catalog->name }}
                </a></li>
            @endif

            <li class="active">{{ $pageData['title'] }}</li>
        </ol>
    </nav>
@endif
```

### 3. Open Graph теги для соцсетей

```blade
@if($pageData)
    <meta property="og:title" content="{{ $pageData['meta_title'] }}">
    <meta property="og:description" content="{{ $pageData['meta_description'] }}">
    <meta property="og:type" content="{{ $pageData['type'] === 'product' ? 'product' : 'website' }}">
    <meta property="og:url" content="{{ url()->current() }}">

    @if($pageData['type'] === 'product' && $pageData['entity']->main_image)
        <meta property="og:image" content="{{ asset($pageData['entity']->main_image) }}">
    @endif
@endif
```

## Расширение функционала

Чтобы добавить поддержку новых типов сущностей, отредактируйте `PageDataService.php`:

```php
// Добавить новый метод поиска
private function findCustomEntityByUrl(string $url): ?array
{
    // Ваша логика поиска
    return [
        'type' => 'custom',
        'id' => $entity->id,
        'title' => $entity->title,
        // ... остальные поля
    ];
}

// Добавить вызов в getPageData()
public function getPageData(): ?array
{
    // ... существующий код

    $customData = $this->findCustomEntityByUrl($currentUrl);
    if ($customData) {
        return $customData;
    }

    return null;
}
```

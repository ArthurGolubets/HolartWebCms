# Модуль Pages & SEO - HolartCMS

Модуль для создания статических и динамических страниц с конструктором блоков и SEO-оптимизацией.

## Возможности

### 1. Типы страниц
- **Статические страницы**: Простой HTML контент с SEO метаданными
- **Динамические страницы**: Страницы, построенные через конструктор блоков

### 2. Типы блоков

#### Hero блоки
- `hero_1`: Основной баннер с заголовком, описанием и кнопкой
- `hero_2`: Hero-блок с видео фоном

#### Контентные блоки
- `text`: Простой текстовый блок
- `rich_text`: Расширенный текстовый блок с HTML
- `cards`: Карточки с выбором источника данных (вручную, товары, инфоблоки)
- `features`: Список преимуществ с иконками

#### Блоки для магазина
- `products`: Вывод товаров из каталога
- `catalogs`: Вывод категорий товаров

#### Медиа блоки
- `slider`: Карусель изображений с автопрокруткой

#### Навигация
- `breadcrumbs`: Хлебные крошки

#### Контейнеры (Layout)
- `container_50_50`: Две колонки по 50%
- `container_33_33_33`: Три равные колонки
- `container_25_75`: Колонки 25% и 75%

### 3. Динамический выбор данных

Блок `cards` поддерживает выбор источника данных:
- **Вручную**: Статические карточки
- **Товары**: Автоматическая загрузка из модуля Shop (если установлен)
- **Инфоблоки**: Загрузка из модуля InfoBlocks (если установлен)

### 4. Вложенные блоки

Контейнеры поддерживают вложенность блоков:
- Добавьте контейнер на страницу
- Внутри контейнера можно размещать другие блоки
- Каждый блок привязан к определенной колонке контейнера

## Установка

```bash
php artisan holartcms:pages-install
```

Команда установки выполнит:
1. Копирование моделей (TPage, TPageBlock, TPageBlockType)
2. Копирование контроллеров
3. Копирование Blade шаблонов в `resources/views/components/blocks`
4. Запуск миграций
5. Создание 13 системных типов блоков
6. Сборку фронтенд ассетов
7. Очистку кэша

## Использование

### В админ-панели

1. Перейдите в раздел **Модули**
2. Установите модуль **Страницы и СЕО**
3. В сайдбаре появится раздел **Страницы и СЕО**
4. Создайте новую страницу:
   - Укажите название, slug генерируется автоматически
   - Выберите тип: статическая или динамическая
   - Заполните SEO поля
   - Для динамической страницы откройте конструктор

### В конструкторе

1. Слева - библиотека доступных блоков
2. Справа - холст страницы
3. Нажмите на блок чтобы добавить его на страницу
4. Настройте блок, нажав на иконку шестеренки
5. Используйте стрелки для изменения порядка блоков
6. Для контейнеров укажите в какую колонку добавляется блок

### На фронтенде

#### Вариант 1: Через компонент

```blade
<x-page-renderer :page="'about'" />
```

или

```blade
@php
    $page = \App\Models\TPage::where('slug', 'about')->first();
@endphp

<x-page-renderer :page="$page" />
```

#### Вариант 2: В контроллере

```php
use App\Models\TPage;

public function show($slug)
{
    $page = TPage::where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

    return view('page', compact('page'));
}
```

```blade
<!-- resources/views/page.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>{{ $page->meta_title ?? $page->title }}</title>
    <meta name="description" content="{{ $page->meta_description }}">
    <meta name="keywords" content="{{ $page->meta_keywords }}">
</head>
<body>
    <x-page-renderer :page="$page" />
</body>
</html>
```

## Кастомизация блоков

Все Blade шаблоны блоков находятся в `resources/views/components/blocks/`.

Вы можете редактировать их для изменения внешнего вида:

```blade
<!-- resources/views/components/blocks/hero-1.blade.php -->
@props(['settings' => []])

<section class="my-custom-hero">
    <!-- Ваша разметка -->
</section>
```

## Создание пользовательских блоков

1. Перейдите в **Типы блоков**
2. Создайте новый тип блока
3. Укажите:
   - Код (slug)
   - Название
   - Категорию
   - Схему полей в JSON
4. Создайте Blade шаблон в `resources/views/components/blocks/{code}.blade.php`

### Пример схемы полей

```json
[
  {
    "name": "title",
    "type": "string",
    "label": "Заголовок"
  },
  {
    "name": "content",
    "type": "textarea",
    "label": "Содержимое"
  },
  {
    "name": "image",
    "type": "image",
    "label": "Изображение"
  },
  {
    "name": "columns",
    "type": "select",
    "label": "Количество колонок",
    "options": [
      {"value": "2", "label": "2"},
      {"value": "3", "label": "3"}
    ]
  }
]
```

### Типы полей

- `string` - текстовое поле
- `number` - числовое поле
- `textarea` - многострочный текст
- `email` - email поле
- `url` - URL поле
- `image` - URL изображения
- `color` - выбор цвета
- `checkbox` / `boolean` - чекбокс
- `select` - выпадающий список

## API Endpoints

```
GET    /admin/api/pages                    - Список страниц
POST   /admin/api/pages                    - Создать страницу
GET    /admin/api/pages/{id}               - Получить страницу
PUT    /admin/api/pages/{id}               - Обновить страницу
DELETE /admin/api/pages/{id}               - Удалить страницу
POST   /admin/api/pages/{id}/duplicate     - Дублировать страницу
POST   /admin/api/pages/generate-slug      - Сгенерировать slug

GET    /admin/api/pages/{pageId}/blocks              - Блоки страницы
POST   /admin/api/pages/{pageId}/blocks              - Добавить блок
PUT    /admin/api/pages/{pageId}/blocks/{id}         - Обновить блок
DELETE /admin/api/pages/{pageId}/blocks/{id}         - Удалить блок
POST   /admin/api/pages/{pageId}/blocks/reorder      - Изменить порядок

GET    /admin/api/page-block-types         - Типы блоков
POST   /admin/api/page-block-types         - Создать тип
PUT    /admin/api/page-block-types/{id}    - Обновить тип
DELETE /admin/api/page-block-types/{id}    - Удалить тип
```

## Удаление модуля

```bash
php artisan holartcms:pages-uninstall
```

С сохранением базы данных:

```bash
php artisan holartcms:pages-uninstall --preserve-db
```

## Структура таблиц

### t_pages
- id
- title
- slug (уникальный)
- type (static/dynamic)
- content (для статических)
- meta_title, meta_description, meta_keywords
- is_active
- sort

### t_page_block_types
- id
- code (уникальный)
- name
- description
- icon
- category
- fields_schema (JSON)
- is_system (защищенные блоки)
- is_container (поддержка вложенности)
- is_active

### t_page_blocks
- id
- page_id
- block_type_id
- parent_block_id (для вложенных блоков)
- container_column (col1, col2, col3)
- settings (JSON)
- sort
- is_active

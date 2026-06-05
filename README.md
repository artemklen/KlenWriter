# KlenWriter

[![Website](https://img.shields.io/badge/Website-klenwriter.arklen.ru-2563eb)](https://klenwriter.arklen.ru)
[![GitHub](https://img.shields.io/badge/GitHub-artemklen%2FKlenWriter-111827)](https://github.com/artemklen/KlenWriter)
[![Download](https://img.shields.io/badge/Download-ZIP-16a34a)](https://github.com/artemklen/KlenWriter/archive/refs/heads/main.zip)

KlenWriter — лёгкий WordPress-плагин для комфортного чтения. Он добавляет на страницы записей и обычные страницы две аккуратные кнопки:

- **Тёмный режим** — мягкая тёмная тема для чтения вечером и ночью.
- **Читать без отвлечений** — режим, который скрывает хедер, футер, сайдбары, виджеты, комментарии и другие выбранные элементы.

Плагин создан для авторских сайтов, блогов, литературных дневников и любых WordPress-проектов, где важно, чтобы тексту ничего не мешало.

## Возможности

- Кнопки появляются только на одиночных записях и страницах.
- На главной, в архивах и в админке кнопки не выводятся.
- Выбор пользователя сохраняется между сессиями.
- `localStorage` имеет приоритет, cookie используется как запасной вариант.
- Тёмный режим и режим без отвлечений работают независимо и могут включаться одновременно.
- В режиме без отвлечений можно выйти отдельной плавающей кнопкой или клавишей `Esc`.
- В настройках можно загрузить авторский логотип через медиабиблиотеку WordPress.
- Все основные тексты кнопок можно переименовать.
- CSS написан осторожно и использует префикс `kw-`, чтобы не ломать тему сайта.

## Требования

- WordPress 5.8 или новее
- PHP 7.4 или новее

## Установка

1. Скопируйте папку `klenwriter` в директорию `wp-content/plugins/`.
2. Откройте админку WordPress.
3. Перейдите в раздел **Плагины**.
4. Активируйте **KlenWriter**.
5. После активации откройте **Настройки → KlenWriter** и настройте внешний вид.

Больше деталей и скриншоты доступны на сайте: <https://klenwriter.arklen.ru>

## Настройки

Страница настроек находится здесь:

**Админка WordPress → Настройки → KlenWriter**

Доступные параметры:

- **Режим по умолчанию**  
  Выберите светлую или тёмную тему для первого посещения.

- **Логотип автора**  
  Загрузите изображение через медиабиблиотеку WordPress. Если логотип включён, он появится над кнопками.

- **CSS-классы для скрытия**  
  Укажите элементы, которые нужно скрывать в режиме без отвлечений.

  Значение по умолчанию:

  ```css
  .site-header, .site-footer, .sidebar, .widget-area, .comments-area, .navigation
  ```

- **Элементы с белым фоном**  
  Укажите классы или ID блоков, которые остаются белыми в тёмном режиме. Например: `.site-content, #content`.

- **Показывать логотип автора над кнопками**  
  Включает или выключает отображение логотипа.

- **Положение кнопок**  
  Выберите, где показывать логотип и кнопки: слева или справа.

- **Цвета тёмного режима**  
  Выберите тёмный цвет фона и светлый цвет текста. Это помогает подстроить режим под дизайн сайта.

- **Размер шрифта в режиме чтения**  
  Укажите размер основного текста в режиме без отвлечений. Допустимый диапазон: 14–32 px.

- **Текст кнопок**  
  Позволяет переименовать кнопки «Тёмный режим» и «Читать без отвлечений».

- **Сбросить настройки по умолчанию**  
  Возвращает стандартные значения плагина.

## Как это выглядит на сайте

На одиночных записях и страницах KlenWriter выводит плавающий блок управления внизу экрана. Пользователь может включить тёмный режим, режим без отвлечений или оба режима сразу.

Если пользователь выбрал режим, плагин запомнит это. При следующем визите состояние восстановится автоматически.

## Безопасность

KlenWriter следует стандартным практикам WordPress:

- настройки сохраняются через `register_setting`;
- пользовательские значения проходят санитизацию;
- выводимые данные экранируются через функции WordPress;
- настройки удаляются при полном удалении плагина через `uninstall.php`;
- JavaScript работает без jQuery.

## Структура проекта

```text
klenwriter/
├── klenwriter.php
├── uninstall.php
├── assets/
│   ├── css/
│   │   ├── dark-mode.css
│   │   ├── distraction-mode.css
│   │   └── admin.css
│   ├── js/
│   │   ├── klenwriter.js
│   │   └── admin.js
│   └── images/
│       └── logo-placeholder.svg
└── includes/
    ├── class-klenwriter-settings.php
    └── class-klenwriter-frontend.php
```

## Для разработчиков

Основной класс плагина находится в `klenwriter.php`. Он подключает:

- `KlenWriter_Settings` — страницу настроек в админке;
- `KlenWriter_Frontend` — фронтенд-кнопки, стили и скрипты.

Фронтенд-режимы управляются CSS-классами:

```css
body.kw-dark-mode
body.kw-distraction-mode
.kw-hidden-by-distraction
```

Если тема использует нестандартную разметку, добавьте нужные селекторы в поле **CSS-классы для скрытия**.

## FAQ

### Почему кнопки не видны на главной странице?

Так задумано. KlenWriter показывает кнопки только на одиночных записях и страницах, чтобы не мешать навигации по сайту.

### Можно ли включить тёмный режим и режим без отвлечений одновременно?

Да. Режимы независимы и могут работать вместе.

### Как выйти из режима без отвлечений?

Нажмите плавающую кнопку **Вернуться** или клавишу `Esc`.

### Что делать, если не скрывается нужный блок темы?

Откройте **Настройки → KlenWriter** и добавьте CSS-селектор этого блока в поле скрываемых элементов.

## Версия

Текущая версия: **1.0**

## Автор

**Артёмка Клён** — писатель.

## Support

For questions and suggestions, visit <https://klenwriter.arklen.ru> or open an issue on GitHub: <https://github.com/artemklen/KlenWriter/issues>.

## Лицензия

GPLv2 or later.

---

# KlenWriter English Version

KlenWriter is a lightweight WordPress plugin designed for comfortable reading. It adds two clean floating controls to single posts and pages:

- **Dark Mode** — a soft dark theme for evening and night reading.
- **Distraction-Free Reading** — a focused mode that hides the header, footer, sidebars, widgets, comments, navigation, and any other elements you choose.

The plugin is made for author websites, personal blogs, literary journals, and any WordPress site where the text should stay at the center.

## Features

- Controls appear only on single posts and pages.
- Controls never appear on the homepage, archives, or inside the WordPress admin area.
- User choices are saved between sessions.
- `localStorage` has priority, with cookies used as a fallback.
- Dark mode and distraction-free mode work independently and can be enabled together.
- Distraction-free mode can be exited with a floating exit button or the `Esc` key.
- A custom author logo can be uploaded through the WordPress Media Library.
- Button labels can be customized from the settings page.
- Styles are intentionally gentle and use the `kw-` prefix to avoid interfering with themes.

## Requirements

- WordPress 5.8 or newer
- PHP 7.4 or newer

## Installation

1. Copy the `klenwriter` folder into `wp-content/plugins/`.
2. Open the WordPress admin dashboard.
3. Go to **Plugins**.
4. Activate **KlenWriter**.
5. After activation, go to **Settings → KlenWriter** and configure the plugin.

More details and screenshots available at <https://klenwriter.arklen.ru>

## Settings

The settings page is available here:

**WordPress Admin → Settings → KlenWriter**

Available options:

- **Default mode**  
  Choose whether first-time visitors should start with the light or dark theme.

- **Author logo**  
  Upload an image through the WordPress Media Library. If enabled, the logo appears above the controls.

- **CSS classes to hide**  
  Define which elements should be hidden in distraction-free mode.

  Default value:

  ```css
  .site-header, .site-footer, .sidebar, .widget-area, .comments-area, .navigation
  ```

- **Elements with a white background**  
  Add classes or IDs for blocks that remain white in dark mode. For example: `.site-content, #content`.

- **Show author logo above buttons**  
  Enables or disables the logo on the frontend.

- **Controls position**  
  Choose where the logo and buttons appear: on the left or on the right.

- **Dark mode colors**  
  Choose the dark background color and the light text color. This helps match dark mode to the site design.

- **Reading mode font size**  
  Set the main text size for distraction-free reading mode. Allowed range: 14–32 px.

- **Button text**  
  Rename the dark mode and distraction-free reading buttons.

- **Reset to defaults**  
  Restores the plugin’s default settings.

## Frontend Behavior

On single posts and pages, KlenWriter displays a floating control panel near the bottom of the screen. Visitors can enable dark mode, distraction-free mode, or both at the same time.

When a visitor chooses a mode, KlenWriter remembers that choice. The same state is restored automatically on the next visit.

## Security

KlenWriter follows standard WordPress security practices:

- settings are registered with `register_setting`;
- user input is sanitized before saving;
- frontend and admin output is escaped with WordPress escaping functions;
- plugin settings are removed through `uninstall.php` when the plugin is deleted;
- JavaScript is written in vanilla JS and does not require jQuery.

## Project Structure

```text
klenwriter/
├── klenwriter.php
├── uninstall.php
├── assets/
│   ├── css/
│   │   ├── dark-mode.css
│   │   ├── distraction-mode.css
│   │   └── admin.css
│   ├── js/
│   │   ├── klenwriter.js
│   │   └── admin.js
│   └── images/
│       └── logo-placeholder.svg
└── includes/
    ├── class-klenwriter-settings.php
    └── class-klenwriter-frontend.php
```

## For Developers

The main plugin class lives in `klenwriter.php`. It initializes:

- `KlenWriter_Settings` — the WordPress admin settings page;
- `KlenWriter_Frontend` — frontend controls, styles, and scripts.

Frontend modes are controlled with these CSS classes:

```css
body.kw-dark-mode
body.kw-distraction-mode
.kw-hidden-by-distraction
```

If a theme uses custom markup, add the required selectors to the **CSS classes to hide** setting.

## FAQ

### Why are the controls not visible on the homepage?

That is intentional. KlenWriter displays controls only on single posts and pages, so it does not interfere with site navigation.

### Can dark mode and distraction-free mode be enabled at the same time?

Yes. The two modes are independent and can work together.

### How do I exit distraction-free mode?

Click the floating **Return** button or press the `Esc` key.

### What should I do if a theme element is not hidden?

Open **Settings → KlenWriter** and add that element’s CSS selector to the hidden elements field.

## Version

Current version: **1.0**

## Author

**Artyomka Klen** — writer.

## Support

For questions and suggestions, visit <https://klenwriter.arklen.ru> or open an issue on GitHub: <https://github.com/artemklen/KlenWriter/issues>.

## License

GPLv2 or later.

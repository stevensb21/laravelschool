# Цветовая схема сайта

## Основные цвета

Сайт использует единую цветовую схему, определенную в файле `resources/css/colors.css`. Все цвета представлены как CSS переменные для легкого изменения и поддержания консистентности.

### Основные цвета
- **Primary Color**: `#6e0104` - Темно-красный (основной цвет)
- **Secondary Color**: `#3d483e` - Темно-зеленый (вторичный цвет)
- **Accent Color**: `#bdc2c5` - Светло-серый (акцентный цвет)
- **Text Color**: `#020202` - Почти черный (основной текст)
- **Background Color**: `#fbfbff` - Почти белый (основной фон)

### Дополнительные цвета
- **Primary Light**: `#8a1a1d` - Светлее основного
- **Primary Dark**: `#4a0002` - Темнее основного
- **Secondary Light**: `#4f5a4f` - Светлее вторичного
- **Secondary Dark**: `#2d352d` - Темнее вторичного
- **Accent Light**: `#d1d6d9` - Светлее акцентного
- **Accent Dark**: `#a8adb0` - Темнее акцентного

## Использование цветов

### Цвета для состояний
- **Success**: `#3d483e` - Зеленый для успеха
- **Warning**: `#f39c12` - Оранжевый для предупреждения
- **Error**: `#6e0104` - Красный для ошибок
- **Info**: `#3498db` - Синий для информации

### Цвета для текста
- **Text Primary**: `#020202` - Основной текст
- **Text Secondary**: `#3d483e` - Вторичный текст
- **Text Muted**: `#bdc2c5` - Приглушенный текст
- **Text Light**: `#fbfbff` - Светлый текст

### Цвета для фонов
- **Background Primary**: `#fbfbff` - Основной фон
- **Background Secondary**: `#bdc2c5` - Вторичный фон
- **Background Dark**: `#3d483e` - Темный фон
- **Background Accent**: `#6e0104` - Акцентный фон

### Цвета для кнопок
- **Button Primary**: `#6e0104` - Основная кнопка
- **Button Primary Hover**: `#8a1a1d` - Основная кнопка при наведении
- **Button Secondary**: `#3d483e` - Вторичная кнопка
- **Button Secondary Hover**: `#4f5a4f` - Вторичная кнопка при наведении
- **Button Accent**: `#bdc2c5` - Акцентная кнопка
- **Button Accent Hover**: `#d1d6d9` - Акцентная кнопка при наведении

### Цвета для форм
- **Input Background**: `#fbfbff` - Фон поля ввода
- **Input Border**: `#bdc2c5` - Граница поля ввода
- **Input Focus**: `#6e0104` - Цвет при фокусе

### Цвета для таблиц
- **Table Header**: `#3d483e` - Заголовок таблицы
- **Table Row Even**: `#fbfbff` - Четная строка
- **Table Row Odd**: `#f5f5f9` - Нечетная строка
- **Table Border**: `#bdc2c5` - Граница таблицы

### Цвета для навигации
- **Navigation Background**: `#3d483e` - Фон навигации
- **Navigation Text**: `#fbfbff` - Текст навигации
- **Navigation Hover**: `#6e0104` - При наведении
- **Navigation Active**: `#6e0104` - Активный элемент

### Цвета для карточек
- **Card Background**: `#fbfbff` - Фон карточки
- **Card Border**: `#bdc2c5` - Граница карточки
- **Card Shadow**: `rgba(110, 1, 4, 0.1)` - Тень карточки

### Цвета для модальных окон
- **Modal Background**: `#fbfbff` - Фон модального окна
- **Modal Overlay**: `rgba(2, 2, 2, 0.5)` - Затемнение

### Цвета для уведомлений
- **Toast Success**: `#3d483e` - Успех
- **Toast Error**: `#6e0104` - Ошибка
- **Toast Warning**: `#f39c12` - Предупреждение
- **Toast Info**: `#3498db` - Информация

### Цвета для статусов
- **Status Active**: `#3d483e` - Активный статус
- **Status Inactive**: `#bdc2c5` - Неактивный статус
- **Status Pending**: `#f39c12` - Ожидающий статус
- **Status Completed**: `#27ae60` - Завершенный статус

### Цвета для прогресс-баров
- **Progress Background**: `#bdc2c5` - Фон прогресс-бара
- **Progress Fill**: `#6e0104` - Заполнение прогресс-бара

### Цвета для ссылок
- **Link Color**: `#6e0104` - Цвет ссылки
- **Link Hover**: `#8a1a1d` - Цвет ссылки при наведении
- **Link Visited**: `#3d483e` - Цвет посещенной ссылки

## Как использовать

### В CSS файлах
```css
/* Импорт цветовых переменных */
@import 'colors.css';

/* Использование переменных */
.my-element {
    background-color: var(--primary-color);
    color: var(--text-light);
    border: 1px solid var(--border-color);
}

.my-button {
    background-color: var(--btn-primary);
    color: var(--text-light);
}

.my-button:hover {
    background-color: var(--btn-primary-hover);
}
```

### В Blade шаблонах
```html
<!-- Подключение в head -->
@vite(['resources/css/colors.css'])

<!-- Использование в inline стилях -->
<div style="background-color: var(--card-bg); color: var(--text-primary);">
    Содержимое
</div>
```

## Изменение цветовой схемы

Для изменения цветовой схемы всего сайта достаточно отредактировать файл `resources/css/colors.css` и изменить значения переменных в секции `:root`.

### Пример изменения основного цвета
```css
:root {
    --primary-color: #your-new-color;
    --primary-light: #your-new-light-color;
    --primary-dark: #your-new-dark-color;
    /* ... остальные переменные */
}
```

## Совместимость

Цветовая схема работает во всех современных браузерах, поддерживающих CSS переменные (CSS Custom Properties). Для старых браузеров рекомендуется использовать fallback значения.

```css
.my-element {
    background-color: #6e0104; /* Fallback для старых браузеров */
    background-color: var(--primary-color); /* Для современных браузеров */
}
``` 
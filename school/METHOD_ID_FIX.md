# Исправление ошибки Foreign Key Violation для method_id

## Проблема

При создании домашнего задания из методпакета возникала ошибка:
```
SQLSTATE[23503]: Foreign key violation: 7 ОШИБКА: INSERT или UPDATE в таблице "home_works" нарушает ограничение внешнего ключа "home_works_method_id_foreign" DETAIL: Ключ (method_id)=(1) отсутствует в таблице "methods".
```

## Причина

1. **Жестко заданное значение**: В коде было жестко задано `method_id = 1`
2. **Отсутствие записи**: В таблице `methods` не существовала запись с `id = 1`
3. **Не nullable поле**: Поле `method_id` было обязательным (не nullable)

## Решение

### 1. Исправлен код в TeacherController

**Было:**
```php
'method_id' => 1, // всегда определён
```

**Стало:**
```php
'method_id' => $methodId, // используем найденный method_id или null
```

### 2. Добавлена инициализация переменной

```php
$methodId = null; // инициализируем как null
```

### 3. Создана миграция для nullable поля

**Файл:** `database/migrations/2025_08_31_000000_make_method_id_nullable_in_home_works.php`

```php
Schema::table('home_works', function (Blueprint $table) {
    // Удаляем ограничение внешнего ключа
    $table->dropForeign(['method_id']);
    
    // Делаем поле nullable
    $table->foreignId('method_id')->nullable()->change();
    
    // Добавляем обратно ограничение с поддержкой null
    $table->foreign('method_id')->references('id')->on('methods')->onDelete('set null');
});
```

### 4. Добавлено логирование

```php
\Log::info('Определение method_id', [
    'lesson_subject' => $lesson->subject,
    'course_id' => $courseId ?? null,
    'method_id' => $methodId,
    'method_found' => $method ? true : false
]);
```

## Логика работы

1. **Поиск курса**: По названию предмета урока ищется курс
2. **Поиск метода**: По найденному курсу ищется соответствующий метод
3. **Установка method_id**: 
   - Если метод найден → `method_id = метод.id`
   - Если метод не найден → `method_id = null`
4. **Создание домашнего задания**: Используется найденный или null method_id

## Результат

- ✅ Домашние задания создаются без ошибок
- ✅ Поддерживаются задания из методпакета (с method_id)
- ✅ Поддерживаются задания преподавателя (method_id = null)
- ✅ Добавлено логирование для диагностики

## Проверка

После исправления попробуйте:
1. Создать урок
2. Выбрать домашнее задание из методпакета
3. Отметить студентов
4. Сохранить

Теперь ошибка не должна возникать.

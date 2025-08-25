# Исправление проблемы с доступом к файлам на Ubuntu

## Текущий статус
✅ Символическая ссылка создана
✅ Файлы существуют в файловой системе
✅ Папка storage имеет права 0775

## Шаг 1: Проверка и исправление прав доступа

```bash
# Переходим в директорию проекта
cd /var/www/laravel/school

# Устанавливаем правильные права для веб-сервера
sudo chown -R www-data:www-data storage/
sudo chown -R www-data:www-data public/storage
sudo chmod -R 755 storage/
sudo chmod -R 755 public/storage

# Проверяем права
ls -la storage/app/public/methodfile/
ls -la public/storage/
```

## Шаг 2: Проверка веб-сервера

### Для Apache:
```bash
# Проверяем, что mod_rewrite включен
sudo a2enmod rewrite
sudo systemctl restart apache2

# Проверяем конфигурацию
sudo apache2ctl -M | grep rewrite
```

### Для Nginx:
```bash
# Проверяем конфигурацию
sudo nginx -t
sudo systemctl reload nginx
```

## Шаг 3: Тестирование доступа к файлу

```bash
# Проверяем конкретный файл
curl -I http://your-domain.com/storage/methodfile/presentation/1756127572_68ac6154c71aa.pdf

# Или через wget
wget --spider http://your-domain.com/storage/methodfile/presentation/1756127572_68ac6154c71aa.pdf
```

## Шаг 4: Проверка логов

```bash
# Логи Laravel
tail -f storage/logs/laravel.log

# Логи Apache
sudo tail -f /var/log/apache2/error.log

# Логи Nginx
sudo tail -f /var/log/nginx/error.log
```

## Шаг 5: Тестирование через браузер

1. Войдите как преподаватель (например, Иванникова Серафима Михайловна, ID: 6)
2. Перейдите к методике по курсу "Биология"
3. Попробуйте открыть файл презентации
4. Проверьте консоль браузера на ошибки

## Шаг 6: Альтернативное решение

Если проблема остается, используйте новый маршрут:

```bash
# Проверьте, что маршрут работает
curl -I http://your-domain.com/methodfile/presentation/1756127572_68ac6154c71aa.pdf
```

## Шаг 7: Проверка SELinux (если используется)

```bash
# Проверяем статус SELinux
sestatus

# Если SELinux активен, разрешаем доступ к файлам
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_unified 1
```

## Шаг 8: Проверка конфигурации PHP

```bash
# Проверяем настройки PHP
php -i | grep -E "(fileinfo|upload_max_filesize|post_max_size)"

# Проверяем, что fileinfo включен
php -m | grep fileinfo
```

## Диагностические команды

```bash
# Проверяем структуру файлов
find storage/app/public/methodfile/ -type f -exec ls -la {} \;

# Проверяем символическую ссылку
ls -la public/storage

# Проверяем права доступа
namei -l /var/www/laravel/school/public/storage/methodfile/presentation/1756127572_68ac6154c71aa.pdf
```

## Если ничего не помогает

1. **Временно отключите проверку прав доступа** в маршруте `/storage/{path}`:
   ```php
   // Закомментируйте проверки и просто возвращайте файл
   return response()->file($filePath);
   ```

2. **Проверьте, работает ли прямой доступ**:
   ```bash
   curl http://your-domain.com/storage/methodfile/presentation/1756127572_68ac6154c71aa.pdf
   ```

3. **Создайте тестовый файл**:
   ```bash
   echo "test" > storage/app/public/methodfile/test.txt
   curl http://your-domain.com/storage/methodfile/test.txt
   ```

## Ожидаемый результат

После выполнения всех шагов:
- Преподаватели должны иметь доступ к файлам курсов, которые они ведут
- Студенты должны иметь доступ к файлам курсов своих групп
- Администраторы должны иметь доступ ко всем файлам
- В логах не должно быть ошибок 404 или 403 
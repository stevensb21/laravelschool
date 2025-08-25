#!/bin/bash

echo "=== Исправление прав доступа для Ubuntu ==="

# Переходим в директорию проекта
cd /var/www/laravel/school

echo "1. Устанавливаем правильные права для веб-сервера..."

# Устанавливаем владельца
sudo chown -R www-data:www-data storage/
sudo chown -R www-data:www-data public/storage

# Устанавливаем права доступа
sudo chmod -R 755 storage/
sudo chmod -R 755 public/storage

# Устанавливаем права на запись для логов
sudo chmod -R 775 storage/logs/
sudo chmod -R 775 storage/framework/

echo "2. Проверяем символическую ссылку..."

# Проверяем и создаем символическую ссылку если нужно
if [ ! -L "public/storage" ]; then
    echo "Создаем символическую ссылку..."
    sudo ln -sf storage/app/public public/storage
fi

echo "3. Проверяем права доступа к файлам..."

# Проверяем конкретный файл
TEST_FILE="storage/app/public/methodfile/presentation/1756127572_68ac6154c71aa.pdf"
if [ -f "$TEST_FILE" ]; then
    echo "✅ Тестовый файл найден: $TEST_FILE"
    echo "   Размер: $(ls -lh "$TEST_FILE" | awk '{print $5}')"
    echo "   Права: $(ls -la "$TEST_FILE" | awk '{print $1}')"
    echo "   Владелец: $(ls -la "$TEST_FILE" | awk '{print $3":"$4}')"
else
    echo "❌ Тестовый файл не найден: $TEST_FILE"
fi

echo "4. Проверяем веб-доступ..."

# Проверяем, что веб-сервер может читать файлы
if sudo -u www-data test -r "$TEST_FILE"; then
    echo "✅ Веб-сервер может читать файл"
else
    echo "❌ Веб-сервер не может читать файл"
fi

echo "5. Проверяем конфигурацию веб-сервера..."

# Определяем веб-сервер
if command -v apache2 >/dev/null 2>&1; then
    echo "Обнаружен Apache"
    sudo a2enmod rewrite >/dev/null 2>&1
    sudo systemctl restart apache2
    echo "✅ Apache перезапущен"
elif command -v nginx >/dev/null 2>&1; then
    echo "Обнаружен Nginx"
    sudo nginx -t && sudo systemctl reload nginx
    echo "✅ Nginx перезагружен"
else
    echo "⚠️  Веб-сервер не обнаружен"
fi

echo "6. Создаем тестовый файл..."

# Создаем тестовый файл
echo "test content" | sudo tee storage/app/public/methodfile/test.txt >/dev/null
sudo chown www-data:www-data storage/app/public/methodfile/test.txt

echo "=== Исправление завершено ==="
echo ""
echo "Теперь протестируйте доступ к файлам:"
echo "1. http://your-domain.com/storage/methodfile/test.txt"
echo "2. http://your-domain.com/storage/methodfile/presentation/1756127572_68ac6154c71aa.pdf"
echo ""
echo "Если файлы не открываются, проверьте логи:"
echo "tail -f storage/logs/laravel.log" 
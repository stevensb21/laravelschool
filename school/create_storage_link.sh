#!/bin/bash

echo "Creating storage symbolic link for Ubuntu/Linux..."

# Переходим в директорию проекта
cd "$(dirname "$0")"

# Проверяем, существует ли уже символическая ссылка
if [ -L "public/storage" ]; then
    echo "Removing existing storage link..."
    rm "public/storage"
fi

# Создаем символическую ссылку
echo "Creating new storage link..."
ln -sf "storage/app/public" "public/storage"

# Проверяем, что ссылка создана
if [ -L "public/storage" ]; then
    echo "Storage link created successfully!"
    echo "Link target: $(readlink public/storage)"
else
    echo "Error: Failed to create storage link"
    exit 1
fi

# Устанавливаем правильные права доступа
echo "Setting proper permissions..."
chmod -R 755 storage/app/public
chown -R www-data:www-data storage/app/public 2>/dev/null || echo "Warning: Could not change ownership (run as root if needed)"

echo "Done!" 
<?php

namespace App\Helpers;

class FileHelper
{
    /**
     * Получает правильный URL для отображения файла или ссылки
     * 
     * @param string $path Путь к файлу или ссылка
     * @return string Полный URL
     */
    public static function getFileUrl($path)
    {
        if (empty($path)) {
            return '';
        }

        // Если это URL (начинается с http или https)
        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        // Если это URL с неправильным префиксом storage/
        if (strpos($path, 'storage/https://') === 0) {
            return substr($path, 9);
        }

        if (strpos($path, 'storage/http://') === 0) {
            return substr($path, 9);
        }

        // Если путь уже содержит /storage/ в начале
        if (strpos($path, '/storage/') === 0) {
            return asset(ltrim($path, '/'));
        }

        // Если путь содержит storage/ в начале (без слеша)
        if (strpos($path, 'storage/') === 0) {
            return asset($path);
        }

        // Для всех остальных случаев добавляем storage/
        return asset('storage/' . ltrim($path, '/'));
    }
}

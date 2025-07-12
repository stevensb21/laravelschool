<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UploadLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Увеличиваем лимиты для загрузки файлов
        ini_set('upload_max_filesize', '20M');
        ini_set('post_max_size', '25M');
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '256M');
        ini_set('max_file_uploads', '50');
        
        return $next($request);
    }
}

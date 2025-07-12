<?php

namespace App\Http;

file_put_contents(base_path('kernel_debug.txt'), 'Kernel loaded: ' . __FILE__);

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // ...
        'isadmin' => \App\Http\Middleware\IsAdmin::class,
        'isteacher' => \App\Http\Middleware\TeacherAccess::class,
        'isstudent' => \App\Http\Middleware\IsStudent::class,
    ];

    public function __construct(...$args)
    {
        file_put_contents(base_path('kernel_debug.txt'), 'Kernel loaded: ' . __FILE__);
        parent::__construct(...$args);
    }
} 
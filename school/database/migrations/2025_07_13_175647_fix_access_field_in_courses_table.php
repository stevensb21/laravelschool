<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $courses = DB::table('courses')->get();
        foreach ($courses as $course) {
            $access = $course->access_;
            // Если это строка — пробуем декодировать
            if (is_string($access)) {
                $decoded = json_decode($access, true);
                // Если после первого декода получилась строка — пробуем ещё раз
                if (is_string($decoded)) {
                    $decoded = json_decode($decoded, true);
                }
                if (is_array($decoded)) {
                    DB::table('courses')->where('id', $course->id)->update([
                        'access_' => $decoded
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Откат не требуется, так как это исправление данных
    }
};

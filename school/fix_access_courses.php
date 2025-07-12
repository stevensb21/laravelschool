<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$courses = \App\Models\Course::all();
foreach ($courses as $course) {
    $val = $course->access_;
    if (is_string($val)) {
        $arr = json_decode($val, true);
        if (is_array($arr)) {
            $course->access_ = $arr;
            $course->save();
            echo "Fixed course ID {$course->id}\n";
        }
    }
}
echo "Done!\n"; 
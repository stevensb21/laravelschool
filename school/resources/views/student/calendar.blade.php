@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/calendar.css'])

<style>
/* Стили для правильного выравнивания заголовка календаря студента */


.calendar-controls form {
    margin: 0;
}






</style>


@endsection

<?php
$colorVars = [
    '--primary-color',
    '--secondary-color',
    '--success-color',
    '--info-color',
    '--status-active',
    '--status-pending',
    '--status-completed',
    '--progress-fill',
    '--link-color',
    '--link-hover',
];
?>

@include('student.nav')



<div class="container">
    <main class="content">
        <div class="calendar-container" >
            <div class="calendar-header">
                <h2>Календарь занятий</h2>
                <div class="calendar-controls">
                    <form method="POST" action="{{ route('calendar.prev.week') }}">
                        @csrf
                        <button name="prevWeek">&lt;</button>
                    </form>
                    <span class="currentMonth">
                        <?php echo date('d.m', strtotime(session('monday'))) . ' - ' . date('d.m', strtotime(session('sunday'))); ?>
                    </span>
                    <form method="POST" action="{{ route('calendar.next.week') }}">
                        @csrf
                        <button name="nextWeek">&gt;</button>
                    </form>
                </div>
            </div>
            <div class="schedule-filters">
                <form method="GET" action="{{ route('calendar') }}" class="filters-form">
                    <div class="select-group">
                        <div class="schedule-filter">
                            <select name="group" onchange="this.form.submit()">
                                <option value="">Все группы</option>
                                <?php foreach ($data['groups'] as $group): ?>
                                    <option value="<?= htmlspecialchars(is_object($group) ? $group->name : $group['name']) ?>" <?= $data['selectedGroup'] === (is_object($group) ? $group->name : $group['name']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(is_object($group) ? $group->name : $group['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="schedule-filter">
                            <select name="subject" onchange="this.form.submit()">
                                <option value="">Все предметы</option>
                                <?php foreach ($data['subjects'] as $subject): ?>
                                    <option value="<?= htmlspecialchars($subject) ?>" <?= $data['selectedSubject'] === $subject ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($subject) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="calendar">
<div class="grid-container-calendar">
    <!-- Заголовок времени -->
    <div class="time-header" style="grid-column: 1; grid-row: 1;"></div>
    <!-- Дни недели -->
    <?php 
    $weekdays = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
    foreach ($weekdays as $i => $day): ?>
        <div class="weekday" style="grid-column: <?= $i+2 ?>; grid-row: 1;">
            <p><?= $day ?></p>
        </div>
    <?php endforeach; ?>
    <!-- Временные отметки (30-минутная сетка) -->
    <?php 
    $times = [];
    for ($h = 8; $h <= 22; $h++) {
        $times[] = sprintf('%02d:00', $h);
        if ($h < 22) $times[] = sprintf('%02d:30', $h);
    }
    foreach ($times as $i => $time): 
    $isHalf = strpos($time, ':30') !== false;
?>
    <div class="time<?= $isHalf ? ' half-hour' : '' ?>" style="grid-column: 1; grid-row: <?= $i+2 ?>;">
            <p><?= $time ?></p>
        </div>
    <?php endforeach; ?>
    <!-- Ячейки календаря -->
    <?php
    for ($slot = 0; $slot < count($times); $slot++) {
        for ($day = 1; $day <= 7; $day++) {
            $cellDate = date('Y-m-d', strtotime(session('monday') . ' +' . ($day-1) . ' days'));
            $style = "grid-column: " . ($day + 1) . "; grid-row: " . ($slot + 2) . "; position:relative; z-index:1;";
            echo '<div class="cell" style="' . $style . '"></div>';
        }
    }
    for ($day = 1; $day <= 7; $day++) {
        $lessonsArray = is_array($data['lessons']) ? $data['lessons'] : (method_exists($data['lessons'], 'all') ? $data['lessons']->all() : []);
        $dayLessons = array_filter($lessonsArray, function($lesson) use ($day) {
            return (date('N', strtotime($lesson->date_)) == $day);
        });
        usort($dayLessons, function($a, $b) {
            return strcmp($a->start_time, $b->start_time);
        });
        $columns = [];
        foreach ($dayLessons as $i => $lesson) {
            $startA = strtotime($lesson->start_time);
            $endA = strtotime($lesson->end_time);
            $col = 0;
            $used = [];
            foreach ($columns as $j => $colLessons) {
                foreach ($colLessons as $other) {
                    $startB = strtotime($other->start_time);
                    $endB = strtotime($other->end_time);
                    if ($startA < $endB && $endA > $startB) {
                        $used[$j] = true;
                    }
                }
            }
            while (isset($used[$col])) $col++;
            $columns[$col][] = $lesson;
            $lesson->_col = $col;
        }
        $maxCols = count($columns);
        foreach ($dayLessons as $idx => $lesson) {
            $startParts = explode(':', $lesson->start_time);
            $endParts = explode(':', $lesson->end_time);
            $startHour = (int)$startParts[0];
            $startMin = (int)$startParts[1];
            $endHour = (int)$endParts[0];
            $endMin = (int)$endParts[1];
            $rowStart = ($startHour - 8) * 2 + 2 + ($startMin >= 30 ? 1 : 0);
            $rowEnd = ($endHour - 8) * 2 + 2 + ($endMin >= 30 ? 1 : 0);
            $rowSpan = max(1, $rowEnd - $rowStart);
            $colorVar = $colorVars[$idx % count($colorVars)];
            $width = 100 / $maxCols;
            $left = $lesson->_col * $width;
            $lessonStyle = "grid-column: " . ($day + 1) . "; grid-row: $rowStart / span $rowSpan; background-color: var($colorVar); color: var(--text-light); border-radius: 10px; z-index:5; margin:2px 0; box-sizing:border-box; display:flex; flex-direction:column; align-items:flex-start; justify-content:flex-start; padding:4px 8px; min-width:80px; pointer-events:auto; border-top:2px solid #fff; box-shadow:0 2px 6px rgba(0,0,0,0.04); position:relative; width:calc($width% - 4px); left:calc($left%);";
            echo '<div class="has-lesson" style="' . $lessonStyle . '">';
            echo '<p style="margin:0;font-weight:600;">' . htmlspecialchars($lesson->subject) . '</p>';
            if (isset($lesson->name_group)) {
                echo '<p style="margin:0;font-size:11px;font-weight:700;color:var(--text-light);text-shadow:0 1px 2px rgba(0,0,0,0.25);background:rgba(0,0,0,0.10);border-radius:4px;padding:0 2px;">' . htmlspecialchars($lesson->name_group) . '</p>';
            }
            echo '</div>';
        }
    }
    ?>
</div>
</div>
        </div>
    </main>
</div>


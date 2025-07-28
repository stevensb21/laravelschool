@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/calendar.css'])

<style>
/* Стили для правильного выравнивания заголовка календаря студента */


.calendar-controls form {
    margin: 0;
}

/* Стили для мобильной версии календаря */
.calendar-mobile {
    display: none;
    background: #f8f9fa;
    border-radius: 12px;
    padding: 16px;
    margin: 16px 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

#weekday-select {
    width: 100%;
    padding: 8px;
    margin-bottom: 12px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    font-size: 16px;
}

.mobile-day-schedule h4 {
    margin: 0 0 10px 0;
    font-size: 20px;
    font-weight: 600;
    color: #22223b;
}

.mobile-lesson-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.mobile-lesson-item {
    background: #fff;
    border-radius: 8px;
    margin-bottom: 10px;
    padding: 10px 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.lesson-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
}

.lesson-time {
    font-weight: bold;
    color: #384034;
}

.lesson-title {
    font-size: 16px;
    color: #22223b;
}

.lesson-teacher, .lesson-group {
    font-size: 14px;
    color: #6c757d;
}

.no-lessons {
    text-align: center;
    color: #b0b0b0;
    font-size: 16px;
    margin: 24px 0;
}

/* Мобильная версия: только на телефонах (до 600px) */
@media (max-width: 600px) {
    .grid-container-calendar { 
        display: none !important; 
    }
    .calendar-mobile { 
        display: block; 
    }
}

/* JavaScript для переключения дней */
</style>

<script>
function showScheduleForDay(day) {
    // Скрываем все расписания
    const schedules = document.querySelectorAll('.mobile-day-schedule');
    schedules.forEach(schedule => {
        schedule.style.display = 'none';
    });
    
    // Показываем выбранное расписание
    const selectedSchedule = document.querySelector(`[data-day="${day}"]`);
    if (selectedSchedule) {
        selectedSchedule.style.display = 'block';
    }
}

// Показываем первый день по умолчанию
document.addEventListener('DOMContentLoaded', function() {
    showScheduleForDay(1);
});
</script>


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

<!-- Мобильная версия календаря -->
<div class="calendar-mobile">
    <select id="weekday-select" onchange="showScheduleForDay(this.value)">
        @php
            $weekdays = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
        @endphp
        @foreach($weekdays as $i => $day)
            <option value="{{ $i+1 }}">{{ $day }}</option>
        @endforeach
    </select>
    @foreach($weekdays as $i => $day)
        @php
            // Собираем все уникальные уроки на этот день
            $uniqueLessons = [];
            foreach ($data['lessons'] as $lesson) {
                if (date('N', strtotime($lesson->date_)) == $i+1) {
                    $uniqueLessons[] = $lesson;
                }
            }
            // Сортируем по времени начала
            usort($uniqueLessons, function($a, $b) {
                return strcmp($a->start_time, $b->start_time);
            });
            $hasLessons = count($uniqueLessons) > 0;
        @endphp
        <div class="mobile-day-schedule" data-day="{{ $i+1 }}" style="display: none;">
            <h4>{{ $day }}</h4>
            @if($hasLessons)
                <ul class="mobile-lesson-list">
                    @foreach ($uniqueLessons as $lesson)
                        <li class="mobile-lesson-item">
                            <div class="lesson-info">
                                <span class="lesson-time">{{ substr($lesson->start_time,0,5) }}-{{ substr($lesson->end_time,0,5) }}</span>
                                <span class="lesson-title">{{ $lesson->subject ?? $lesson->name_group ?? '' }}</span>
                                @if(isset($lesson->teacher))
                                    <span class="lesson-teacher">{{ $lesson->teacher }}</span>
                                @endif
                                @if(isset($lesson->name_group))
                                    <span class="lesson-group">{{ $lesson->name_group }}</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="no-lessons">Уроков сегодня нет</div>
            @endif
        </div>
    @endforeach
</div>
</div>
        </div>
    </main>
</div>


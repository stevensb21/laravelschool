@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/calendar.css'])
@endsection

@include('teacher.nav')

<?php
    $lessons = $data['lessons'];
    $groups = $data['groups'];
    $subjects = $data['subjects'];
    $user = $data['user'];
    $schedule = $data['schedule'];
    $selectedGroup = $data['selectedGroup'];
    $selectedSubject = $data['selectedSubject'];
    $isTeacher = true;
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

<div class="container">
    <main class="content">
        <div class="calendar-container">
            <div class="calendar-header">
                <h2>Управление расписанием</h2>
                <div class="calendar-controls">
                    <form method="POST" action="{{ route('teacher.calendar.prev.week') }}">
                        @csrf
                        <button name="prevWeek">&lt;</button>
                    </form>
                    <span class="currentMonth">
                        <?php echo date('d.m', strtotime(session('monday'))) . ' - ' . date('d.m', strtotime(session('sunday'))); ?>
                    </span>
                    <form method="POST" action="{{ route('teacher.calendar.next.week') }}">
                        @csrf
                        <button name="nextWeek">&gt;</button>
                    </form>
                </div>
            </div>
            <div class="schedule-filters">
                <form method="GET" action="{{ route('teacher.calendar') }}" class="filters-form">
                    <div class="select-group">
                        <div class="schedule-filter">
                            <select name="group" onchange="this.form.submit()">
                                <option value="">Все группы</option>
                                @foreach ($groups as $group)
                                    <option value="{{ is_object($group) ? $group->name : $group['name'] }}" {{ $selectedGroup === (is_object($group) ? $group->name : $group['name']) ? 'selected' : '' }}>
                                        {{ is_object($group) ? $group->name : $group['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="schedule-filter">
                            <select name="subject" onchange="this.form.submit()">
                                <option value="">Все предметы</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ is_object($subject) ? $subject->name : $subject }}" {{ $selectedSubject === (is_object($subject) ? $subject->name : $subject) ? 'selected' : '' }}>
                                        {{ is_object($subject) ? $subject->name : $subject }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
                @if(request('edit_mode'))
                    <form method="GET" action="{{ route('teacher.calendar') }}" class="edit-form" style="display:inline;">
                        <button type="submit" class="edit-button" style="background:#b0b0b0;">Просмотр</button>
                    </form>
                @else
                    <form method="GET" action="{{ route('teacher.calendar') }}" class="edit-form">
                        <input type="hidden" name="edit_mode" value="1">
                        <button type="submit" class="edit-button">Изменить</button>
                    </form>
                @endif
            </div>
            <div class="calendar">
<div class="grid-container-calendar">
    <div class="time-header" style="grid-column: 1; grid-row: 1;"></div>
    <?php 
    $weekdays = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
    foreach ($weekdays as $i => $day): ?>
        <div class="weekday" style="grid-column: <?= $i+2 ?>; grid-row: 1;">
            <p><?= $day ?></p>
        </div>
    <?php endforeach; ?>
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
    <?php
    for ($slot = 0; $slot < count($times); $slot++) {
        for ($day = 1; $day <= 7; $day++) {
            $cellDate = date('Y-m-d', strtotime(session('monday') . ' +' . ($day-1) . ' days'));
            $style = "grid-column: " . ($day + 1) . "; grid-row: " . ($slot + 2) . "; position:relative; z-index:1;";
            echo '<div class="cell" style="' . $style . '">';
            if (request('edit_mode')) {
                echo '<button type="button" class="add-btn" style="position:absolute;bottom:4px;right:4px;z-index:10;" onclick="showModal(\'' . $cellDate . '\', \'' . $times[$slot] . '\')">+</button>';
            }
            echo '</div>';
        }
    }
    for ($day = 1; $day <= 7; $day++) {
        $lessonsArray = is_array($lessons) ? $lessons : (method_exists($lessons, 'all') ? $lessons->all() : []);
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
                if (request('edit_mode')) {
                echo '<form method="POST" action="' . route('calendar.delete-lesson') . '" class="delete-lesson-form" style="position: absolute; left: 0; bottom: 0; z-index: 20;">';
                    echo csrf_field();
                echo '<input type="hidden" name="lesson_id" value="' . $lesson->id . '">';
                echo '<button type="submit" name="delete_lesson" class="delete-btn">&times;</button>';
                    echo '</form>';
                $lessonDate = date('Y-m-d', strtotime($lesson->date_));
                $startTime = $lesson->start_time;
                echo '<button type="button" class="add-btn lesson-add-btn" style="position:absolute;right:6px;bottom:6px;z-index:21;" onclick="showModal(\'' . $lessonDate . '\', \'' . $startTime . '\')">+</button>';
            }
            echo '</div>';
        }
    }
    ?>
</div>
</div>

<!-- Модальное окно для добавления урока -->
<div id="addLessonModal" class="lesson-modal" style="display: none;">
    <div class="modal-content">
        <h3>Добавить урок</h3>
        <form method="POST" action="{{ route('calendar.add-lesson') }}">
            @csrf
            <input type="hidden" name="date" id="modalDate">
            @if(request('edit_mode'))
                <input type="hidden" name="edit_mode" value="1">
            @endif
            <div class="form-group">
                <label>Время начала:</label>
                <select name="start_time" id="modalStartTime" required>
                    <!-- Опции будут добавлены через JavaScript -->
                </select>
            </div>
            <div class="form-group">
                <label>Предмет:</label>
                <select name="subject" required>
                    <option value="">Выберите предмет</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject }}">{{ $subject }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Группа:</label>
                <select name="name_group" required>
                    <option value="">Выберите группу</option>
                    @foreach($groups as $group)
                        <option value="{{ is_object($group) ? $group->name : $group['name'] }}">{{ is_object($group) ? $group->name : $group['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Преподаватель:</label>
                <input type="text" name="teacher" value="{{ $user->fio ?? '' }}" readonly>
            </div>
            <div class="form-group">
                <label>Время окончания:</label>
                <select name="end_time" id="modalEndTime" required>
                    <!-- Опции будут добавлены через JavaScript -->
                </select>
            </div>
            <div class="form-buttons">
                <button type="submit" name="save_lesson">Сохранить</button>
                <button type="button" class="cancel-btn" onclick="closeModal()">Отмена</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showModal(date, startTime) {
        const modal = document.getElementById('addLessonModal');
        const dateInput = document.getElementById('modalDate');
        const startTimeSelect = document.getElementById('modalStartTime');
        const endTimeSelect = document.getElementById('modalEndTime');
        dateInput.value = date;
        startTimeSelect.innerHTML = '';
        endTimeSelect.innerHTML = '';
        for (let h = 8; h <= 22; h++) {
            let hourStr = String(h).padStart(2, '0');
            startTimeSelect.add(new Option(hourStr + ':00', hourStr + ':00'));
            if (h < 22) startTimeSelect.add(new Option(hourStr + ':30', hourStr + ':30'));
        }
        for (let h = 8; h <= 22; h++) {
            let hourStr = String(h).padStart(2, '0');
            endTimeSelect.add(new Option(hourStr + ':00', hourStr + ':00'));
            if (h < 22) endTimeSelect.add(new Option(hourStr + ':30', hourStr + ':30'));
        }
        if (startTime) startTimeSelect.value = startTime;
        modal.style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('addLessonModal').style.display = 'none';
    }
    window.onclick = function(event) {
        const modal = document.getElementById('addLessonModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

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
                    $hasLessons = false;
                    for ($hour = 8; $hour <= 22; $hour++) {
                        if (!empty($data['schedule'][$i+1][$hour])) {
                            $hasLessons = true;
                            break;
                        }
                    }
                @endphp
                <div class="mobile-day-schedule" data-day="{{ $i+1 }}" style="display: none;">
                    <h4>{{ $day }}</h4>
                    @if($hasLessons)
                        <ul class="mobile-lesson-list">
                            @for ($hour = 8; $hour <= 22; $hour++)
                                @php
                                    $lesson = $data['schedule'][$i+1][$hour] ?? null;
                                @endphp
                                @if ($lesson)
                                    <li>
                                        <span class="lesson-time">{{ $hour }}:00</span>
                                        <span class="lesson-title">{{ $lesson['subject'] ?? $lesson['name_group'] ?? '' }}</span>
                                        @if(isset($lesson['teacher']))
                                            <span class="lesson-teacher">{{ $lesson['teacher'] }}</span>
                                        @endif
                                        @if(isset($lesson['name_group']))
                                            <span class="lesson-group">{{ $lesson['name_group'] }}</span>
                                        @endif
                                    </li>
                                @endif
                            @endfor
                        </ul>
                    @else
                        <div class="no-lessons">Уроков сегодня нет</div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Стили для мобильной версии -->
        <style>
        .calendar-mobile {
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
        .mobile-lesson-list li {
            background: #fff;
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 10px 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            gap: 2px;
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

        .calendar-mobile { 
            display: none; 
        }

        .calendar-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .calendar-controls form {
            margin: 0;
        }
        .currentMonth {
            font-size: 20px;
            font-weight: 500;
            margin: 0 10px;
        }


        @media (max-width: 768px) {
          .calendar-desktop, .grid-container-calendar { display: none !important; }
          .calendar-mobile { display: block; }
        }
        </style>

        <!-- JS для переключения дней -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('weekday-select');
            function showScheduleForDay(day) {
                document.querySelectorAll('.mobile-day-schedule').forEach(el => {
                    el.style.display = (el.dataset.day === day) ? 'block' : 'none';
                });
            }
            if (select) {
                showScheduleForDay(select.value);
                select.addEventListener('change', function() {
                    showScheduleForDay(this.value);
                });
            }
        });
        </script>
    </main>
</div> 
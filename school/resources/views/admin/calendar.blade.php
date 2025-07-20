@extends('admin.layouts.head')
@section('head')

@vite(['resources/css/calendar.css'])
@include('admin.layouts.adminNav')

<?php
    $lessons = $data['lessons'];
    $groups = $data['groups'];
    $subjects = $data['subjects'];
    $teachers = $data['teachers'];
    $user = $data['user'];
    $schedule = $data['schedule'];
    $selectedGroup = $data['selectedGroup'];
    $selectedSubject = $data['selectedSubject'];
    $selectedTeacher = $data['selectedTeacher'];
    $isAdmin = $data['isAdmin'] ?? false;
    $isTeacher = $data['isTeacher'] ?? false;
    $isStudent = $data['isStudent'] ?? false;
?>

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
 

<div class="container">
    <main class="content">
        <div class="calendar-container">
            <div class="calendar-header">
                <h2>Управление расписанием</h2>
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
                        @if($isAdmin || $isTeacher)
                            <div class="schedule-filter">
                                <select name="group" onchange="this.form.submit()">
                                    <option value="">Все группы</option>
                                    <?php foreach ($groups as $group): ?>
                                        <option value="<?= htmlspecialchars(is_object($group) ? $group->name : $group['name']) ?>" <?= $selectedGroup === (is_object($group) ? $group->name : $group['name']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars(is_object($group) ? $group->name : $group['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        @endif

                        @if($isAdmin || $isStudent)
                            <div class="schedule-filter">
                                <select name="teacher" onchange="this.form.submit()">
                                    <option value="">Все преподаватели</option>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= htmlspecialchars($teacher['fio']) ?>" <?= $selectedTeacher === $teacher['fio'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($teacher['fio']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        @endif

                        @if($isAdmin || $isTeacher || $isStudent)
                            <div class="schedule-filter">
                                <select name="subject" onchange="this.form.submit()">
                                    <option value="">Все предметы</option>
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= htmlspecialchars($subject) ?>" <?= $selectedSubject === $subject ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($subject) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        @endif
                    </div>                   
                </form>
                @if($isAdmin)
                    @if($data['edit_mode'] ?? false)
                        <form method="GET" action="{{ route('calendar') }}" class="edit-form" style="display:inline;">
                            <button type="submit" class="edit-button" style="background:#b0b0b0;">Просмотр</button>
                        </form>
                    @else
                        <form method="GET" action="{{ route('calendar') }}" class="edit-form">
                            <input type="hidden" name="edit_mode" value="1">
                            <button type="submit" class="edit-button">Изменить</button>
                        </form>
                    @endif
                @endif
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
    // 1. Сетка и кнопки +
    for ($slot = 0; $slot < count($times); $slot++) {
        for ($day = 1; $day <= 7; $day++) {
            $cellDate = date('Y-m-d', strtotime(session('monday') . ' +' . ($day-1) . ' days'));
            $style = "grid-column: " . ($day + 1) . "; grid-row: " . ($slot + 2) . "; position:relative; z-index:1;";
            echo '<div class="cell" style="' . $style . '">';
            if ($data['edit_mode'] ?? false) {
                echo '<button type="button" class="add-btn" style="position:absolute;bottom:4px;right:4px;z-index:10;" onclick="showModal(\'' . $cellDate . '\', \'' . $times[$slot] . '\')">+</button>';
            }
            echo '</div>';
        }
    }
    // 2. Уроки с точным позиционированием
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
                if ($data['edit_mode'] ?? false) {
                echo '<form method="POST" action="' . route('calendar.delete-lesson') . '" class="delete-lesson-form" style="position: absolute; left: 0; bottom: 0; z-index: 20;">';
                    echo csrf_field();
                echo '<input type="hidden" name="lesson_id" value="' . $lesson->id . '">';
                echo '<button type="submit" name="delete_lesson" class="delete-btn">&times;</button>';
                    echo '</form>';
                // Кнопка добавления параллельного урока (справа снизу)
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
            @if($data['edit_mode'] ?? false)
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
                        <option value="{{ $group['name'] }}">{{ $group['name'] }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label>Преподаватель:</label>
                <select name="teacher" required>
                    <option value="">Выберите преподавателя</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher['fio'] }}">{{ $teacher['fio'] }}</option>
                    @endforeach
                </select>
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
    
    // Отладочная информация
    console.log('showModal called with:', { date, startTime });
    
    // Устанавливаем дату
    dateInput.value = date;
    // Очищаем и заполняем select времени начала
    startTimeSelect.innerHTML = '';
    let startHour = 8, endHour = 22;
    for (let h = startHour; h <= endHour; h++) {
        let hourStr = String(h).padStart(2, '0');
        startTimeSelect.add(new Option(hourStr + ':00', hourStr + ':00'));
        if (h < endHour) startTimeSelect.add(new Option(hourStr + ':30', hourStr + ':30'));
    }
    // Выделяем нужное время
    if (startTime) {
        startTimeSelect.value = startTime;
    }
    // Сразу обновляем значения конца урока
    updateEndOptions();
    // Сброс значения конца урока
    endTimeSelect.value = '';
    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('addLessonModal').style.display = 'none';
}

// Закрытие модального окна при клике вне его
window.onclick = function(event) {
    const modal = document.getElementById('addLessonModal');
    if (event.target == modal) {
        closeModal();
    }
}

function generateTimes(from = '08:00', to = '22:00') {
    const times = [];
    let [h, m] = from.split(':').map(Number);
    const [endH, endM] = to.split(':').map(Number);
    while (h < endH || (h === endH && m <= endM)) {
        times.push((h < 10 ? '0' : '') + h + ':' + (m === 0 ? '00' : '30'));
        m += 30;
        if (m === 60) { h++; m = 0; }
    }
    return times;
}

function updateEndOptions() {
    const startTimeSelect = document.getElementById('modalStartTime');
    const endTimeSelect = document.getElementById('modalEndTime');
    const allTimes = generateTimes();
    const startValue = startTimeSelect.value;
    endTimeSelect.innerHTML = '';
    let add = false;
    allTimes.forEach(time => {
        if (time === startValue) add = true;
        if (add) {
            const opt = document.createElement('option');
            opt.value = time;
            opt.textContent = time;
            endTimeSelect.appendChild(opt);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const startTimeSelect = document.getElementById('modalStartTime');
    const endTimeSelect = document.getElementById('modalEndTime');
    if (startTimeSelect && endTimeSelect) {
        startTimeSelect.addEventListener('change', updateEndOptions);
        // Инициализация при открытии модалки
        updateEndOptions();
    }
});
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
                            <div style="font-size: 10px; color: #999;"></div>
                                    @if(true)
                                        <div class="mobile-lesson-actions">
                                            <div>
                                        <button class="mobile-delete-btn" onclick="deleteLesson('{{ $lesson->id }}')">Удалить</button>
                                            </div>
                                        </div>
                                    @endif
                            </li>
                    @endforeach
                </ul>
            @else
                <div class="no-lessons">Уроков сегодня нет</div>
            @endif
            @if($isAdmin && ($data['edit_mode'] ?? false))
                <div class="mobile-add-lesson">
                    <button type="button" class="mobile-add-btn" onclick="showMobileAddModal('{{ date('Y-m-d', strtotime(session('monday') . ' +' . ($i) . ' days')) }}')">
                        + Добавить урок
                    </button>
                </div>
            @endif
        </div>
    @endforeach
</div>

<!-- Стили для мобильной версии -->
<style>
.calendar-mobile {
    display: none;
    background: #f8f9fa;
    border-radius: 12px;
    padding: 16px;
    margin: 16px 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.mobile-edit-controls {
    margin-bottom: 16px;
    text-align: center;
}

.mobile-edit-btn {
    background: var(--btn-primary);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
    max-width: 200px;
}

.mobile-edit-btn:hover {
    background: var(--btn-primary-hover);
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

.mobile-lesson-actions {
    display: flex;
    gap: 8px;
}

.mobile-delete-btn {
    background: var(--btn-primary);
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    white-space: nowrap;
}

.mobile-delete-btn:hover {
    background: var(--btn-primary-hover);
}

.mobile-add-lesson {
    margin-top: 16px;
    text-align: center;
}

.mobile-add-btn {
    background: var(--btn-primary);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
    max-width: 200px;
}

.mobile-add-btn:hover {
    background: var(--btn-primary-hover);
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
  .calendar-desktop, .grid-container-calendar { display: none !important; }
  .calendar-mobile { display: block; }
}


/* Стили для модального окна */
.lesson-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    max-width: 90%;
    max-height: 90%;
    overflow-y: auto;
    width: 400px;
}

.modal-content h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #333;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
}

.form-group select,
.form-group input,
.schedule-filter select {
    width: 100%;
    padding: 8px;
    border: 1.5px solid var(--input-border);
    border-radius: 6px;
    font-size: 16px;
    background: var(--input-bg);
    color: var(--text-primary);
    transition: border 0.2s;
}
.form-group select:focus,
.schedule-filter select:focus {
    border-color: var(--input-focus);
    outline: none;
}

.form-buttons {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.form-buttons button {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
}

.form-buttons button[type="submit"] {
    background: var(--btn-primary);
    color: white;
}

.form-buttons button[type="submit"]:hover {
    background: var(--btn-primary-hover);
}

.cancel-btn {
    background: var(--btn-secondary);
    color: white;
}

.cancel-btn:hover {
    background: var(--btn-secondary-hover);
}

@media (max-width: 600px) {
    .modal-content {
        width: 95%;
        margin: 10px;
        padding: 15px;
    }
    
    .form-buttons {
        flex-direction: column;
    }
    
    .form-buttons button {
        margin-bottom: 10px;
    }
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

// Функция для показа модального окна добавления урока в мобильной версии
function showMobileAddModal(date) {
    const modal = document.getElementById('addLessonModal');
    const dateInput = document.getElementById('modalDate');
    const startTimeSelect = document.getElementById('modalStartTime');
    const endTimeSelect = document.getElementById('modalEndTime');
    
    // Устанавливаем дату
    dateInput.value = date;
    
    // Очищаем и заполняем select времени начала
    startTimeSelect.innerHTML = '';
    for (let hour = 8; hour <= 21; hour++) {
        const currentHour = `${String(hour).padStart(2, '0')}:00`;
        startTimeSelect.add(new Option(currentHour, currentHour));
        
        const currentHalfHour = `${String(hour).padStart(2, '0')}:30`;
        startTimeSelect.add(new Option(currentHalfHour, currentHalfHour));
    }
    
    // Очищаем и заполняем select времени окончания
    endTimeSelect.innerHTML = '';
    for (let hour = 8; hour <= 22; hour++) {
        const currentHour = `${String(hour).padStart(2, '0')}:00`;
        endTimeSelect.add(new Option(currentHour, currentHour));
        
        const currentHalfHour = `${String(hour).padStart(2, '0')}:30`;
        endTimeSelect.add(new Option(currentHalfHour, currentHalfHour));
    }
    
    modal.style.display = 'flex';
}
</script>

@endsection
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
    
    <!-- Временные отметки -->
    <?php 
    $times = ['8:00', '9:00', '10:00', '11:00', '12:00', '13:00', '14:00', 
                '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'];
    foreach ($times as $i => $time): ?>
        <div class="time" style="grid-column: 1; grid-row: <?= $i+2 ?>;">
            <p><?= $time ?></p>
        </div>
    <?php endforeach; ?>
    
    <!-- Ячейки календаря -->
    <?php
    for ($hour = 8; $hour <= 22; $hour++) {
        for ($day = 1; $day <= 7; $day++) {
            $lesson = $schedule[$day][$hour];
            echo("<script>console.log('php_array: ".json_encode($schedule[$day][$hour])."');</script>");
            
            // Инициализируем переменные значениями по умолчанию
            $isFirstHour = false;
            $endHour = 0;
            $endMinute = 0;
            $startMinute = 0;
            $isLastHour = false;
            $isEndHour = false;
            
            // Проверяем, существует ли урок, прежде чем обращаться к его данным
            if ($lesson) {
                $isFirstHour = ($hour == (int)date('G', strtotime($lesson['start_time'])));
                $endHour = (int)date('G', strtotime($lesson['end_time']));
                $endMinute = (int)date('i', strtotime($lesson['end_time']));
                $startMinute = (int)date('i', strtotime($lesson['start_time']));
                $isLastHour = ($hour == $endHour || ($hour == $endHour - 1 && $endMinute == 0));
                
                // Проверяем, является ли текущий час последним часом урока
                $isEndHour = ($hour == $endHour);
                
                // Проверяем, начинается ли следующий урок в этом же часе
                $nextLessonInSameHour = false;
                if (isset($schedule[$day][$hour+1])) {
                    $nextLesson = $schedule[$day][$hour+1];
                    $nextStartHour = (int)date('G', strtotime($nextLesson['start_time']));
                    $nextStartMinute = (int)date('i', strtotime($nextLesson['start_time']));
                    
                    if ($nextStartHour == $hour && $nextStartMinute == 30) {
                        $nextLessonInSameHour = true;
                    }
                }
            }
            
            echo("<script>console.log('hour: ".json_encode($hour)."');</script>");
            echo("<script>console.log('day: ".json_encode($day)."');</script>");
            echo("<script>console.log('isFirstHour: ".json_encode($isFirstHour)."');</script>");
            echo("<script>console.log('isLastHour: ".json_encode($isLastHour)."');</script>");
            echo("<script>console.log('endHour: ".json_encode($endHour)."');</script>");
            echo("<script>console.log('endMinute: ".json_encode($endMinute)."');</script>");
            
            $style = "grid-column: " . ($day + 1) . "; grid-row: " . ($hour - 6) . "; border-radius: 10px; color: var(--btn-primary);";
            
            if ($lesson) {
                $start_minute = (int)date('i', strtotime($lesson['start_time']));
                $end_minute = (int)date('i', strtotime($lesson['end_time']));
                $current_hour = (int)date('G', strtotime($lesson['start_time']));
                
                // Определяем, является ли текущий час первым или последним для урока
                $isFirstHour = ($hour == $current_hour);
                $isLastHour = ($hour == (int)date('G', strtotime($lesson['end_time'])));
                
                // Базовый стиль для ячейки
                $style .= "background-color: var(--bg-secondary); color: var(--btn-primary);";
                
                // Сбрасываем класс ячейки для каждой новой итерации
                $cellClass = 'cell has-lesson';
                
                // Если урок начинается в половине часа
                if ($isFirstHour && $start_minute == 30) {
                    $style = "grid-column: " . ($day + 1) . "; grid-row: " . ($hour - 6) . "; border-radius: 10px; color: var(--btn-primary);";
                    $style .= "background: linear-gradient(to bottom, transparent 50%, var(--bg-secondary) 50%); color: var(--btn-primary);";
                    
                    // Проверяем, есть ли предыдущий урок
                    $hasPrevLesson = false;
                    foreach ($lessons as $prevLesson) {
                        if ($prevLesson['date_'] === $lesson['date_'] &&
                            (int)date('G', strtotime($prevLesson['end_time'])) === $hour &&
                            (int)date('i', strtotime($prevLesson['end_time'])) === 30) {
                            $hasPrevLesson = true;
                            break;
                        }
                    }
                    
                    if ($hasPrevLesson) {
                        $style = "grid-column: " . ($day + 1) . "; grid-row: " . ($hour - 6) . "; border-radius: 10px; color: var(--btn-primary);";
                        $style .= "background: linear-gradient(to bottom, var(--bg-secondary) 50%, var(--bg-secondary) 50%); color: var(--btn-primary);";
                        $cellClass = 'cell has-lesson has-split';
                    }
                }
                
                // Если урок заканчивается в половине часа
                if ($isLastHour && $end_minute == 30) {
                    $style = "grid-column: " . ($day + 1) . "; grid-row: " . ($hour - 6) . "; border-radius: 10px; color: var(--btn-primary);";
                    $style .= "background: linear-gradient(to bottom, var(--bg-secondary) 50%, transparent 50%);color: var(--btn-primary);";
                    
                    // Проверяем, есть ли следующий урок
                    $hasNextLesson = false;
                    foreach ($lessons as $nextLesson) {
                        if ($nextLesson['date_'] === $lesson['date_'] &&
                            (int)date('G', strtotime($nextLesson['start_time'])) === $hour &&
                            (int)date('i', strtotime($nextLesson['start_time'])) === 30) {
                            $hasNextLesson = true;
                            break;
                        }
                    }
                    
                    if ($hasNextLesson) {
                        $style = "grid-column: " . ($day + 1) . "; grid-row: " . ($hour - 6) . "; border-radius: 10px; color: var(--btn-primary);";
                        $style .= "background: linear-gradient(to bottom, var(--bg-secondary) 50%, var(--bg-secondary) 50%); color: var(--btn-primary);";
                        $cellClass = 'cell has-lesson has-split';
                    }
                }
                
                // Добавляем скругления для начала и конца урока
                if ($isFirstHour && $start_minute == 0) {
                    $style .= "border-radius: 10px 10px 0 0; color: var(--btn-primary);";
                }
                if ($isLastHour && $end_minute == 0) {
                    $style .= "border-radius: 0 0 10px 10px; color: var(--btn-primary);";
                }
                if ($isFirstHour && $isLastHour && $start_minute == 0 && $end_minute == 0) {
                    $style .= "border-radius: 10px; color: var(--btn-primary);";
                }
                
                $style .= "position: relative;";
            } else {
                $cellClass = 'cell';
            }
            
            echo '<div class="' . $cellClass . '" style="' . $style . '">';
            
            if ($lesson) {
                // Отображение текста урока
                if ($isFirstHour && $start_minute == 30) {
                    // Текст для урока, начинающегося в половине часа
                    echo '<div class="half-hour-text" style="top: 50%; height: 50%; background-color: transparent; color: var(--btn-primary);">';
                    echo '<p>' . htmlspecialchars($lesson['subject']) . ' - ' . 
                            (isset($lesson['name_group']) ? htmlspecialchars($lesson['name_group']) : '') . '</p>';
                    echo '</div>';
                } elseif ($isLastHour && $end_minute == 30) {
                    // Текст для урока, заканчивающегося в половине часа
                    echo '<div class="half-hour-text" style="top: 0; height: 50%; background-color: transparent; color: var(--btn-primary);">';
                    echo '<p>' . htmlspecialchars($lesson['subject']) . ' - ' . 
                            (isset($lesson['name_group']) ? htmlspecialchars($lesson['name_group']) : '') . '</p>';
                    echo '</div>';
                } else {
                    // Обычный текст для полного часа
                    echo '<p>' . htmlspecialchars($lesson['subject']) . '</p>';
                    if (isset($lesson['name_group'])) {
                        echo '<p>' . htmlspecialchars($lesson['name_group']) . '</p>';
                    }
                }
                
                // Кнопка удаления для режима редактирования
                if ($data['edit_mode'] ?? false) {
                    echo '<form method="POST" action="' . route('calendar.delete-lesson') . '" class="delete-lesson-form" style="position: absolute; top: 2px; right: 2px; z-index: 20;">';
                    echo csrf_field();
                    echo '<input type="hidden" name="lesson_id" value="' . $lesson['id'] . '">';
                    echo '<button type="submit" name="delete_lesson" class="delete-btn" style="background: none; border: none; color: #ff4444; cursor: pointer; font-size: 16px; padding: 2px 6px;">&times;</button>';
                    echo '</form>';
                }
            } else if ($data['edit_mode'] ?? false) {
                echo '<button type="button" class="add-btn" onclick="showModal(\'' . date('Y-m-d', strtotime(session('monday') . ' +' . ($day-1) . ' days')) . '\', \'' . sprintf('%02d:00', $hour) . '\')">+</button>';
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
    
    // Устанавливаем дату
    dateInput.value = date;
    
    // Очищаем и заполняем select времени начала
    startTimeSelect.innerHTML = '';
    const hour = parseInt(startTime.split(':')[0]);
    
    if (hour > 8) {
        const prevHalfHour = `${String(hour-1).padStart(2, '0')}:30`;
        startTimeSelect.add(new Option(prevHalfHour, prevHalfHour));
    }
    
    const currentHour = `${String(hour).padStart(2, '0')}:00`;
    startTimeSelect.add(new Option(currentHour, currentHour, true, true));
    
    const currentHalfHour = `${String(hour).padStart(2, '0')}:30`;
    startTimeSelect.add(new Option(currentHalfHour, currentHalfHour));
    
    // Очищаем и заполняем select времени окончания
    endTimeSelect.innerHTML = '';
    let currentTime = new Date(`2000-01-01T${startTime}`);
    const endTime = new Date('2000-01-01T22:00:00');
    
    while (currentTime <= endTime) {
        const timeStr = currentTime.toTimeString().slice(0, 5);
        endTimeSelect.add(new Option(timeStr, timeStr));
        currentTime.setMinutes(currentTime.getMinutes() + 30);
    }
    
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
                if (!empty($schedule[$i+1][$hour])) {
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
                            $lesson = $schedule[$i+1][$hour] ?? null;
                        @endphp
                        @if ($lesson)
                            <li class="mobile-lesson-item">
                                <div class="lesson-info">
                                    <span class="lesson-time">{{ $hour }}:00</span>
                                    <span class="lesson-title">{{ $lesson['subject'] ?? $lesson['name_group'] ?? '' }}</span>
                                    @if(isset($lesson['teacher']))
                                        <span class="lesson-teacher">{{ $lesson['teacher'] }}</span>
                                    @endif
                                    @if(isset($lesson['name_group']))
                                        <span class="lesson-group">{{ $lesson['name_group'] }}</span>
                                    @endif
                                </div>
                                <!-- Debug info -->
                                <div style="font-size: 10px; color: #999;">
                                    <!-- isAdmin: {{ $isAdmin ? 'true' : 'false' }}
                                    edit_mode: {{ ($data['edit_mode'] ?? false) ? 'true' : 'false' }} -->
                                </div>
                                
                                    @if(true)
                                    
                                        <div class="mobile-lesson-actions">
                                            <div>
                                                <button class="mobile-delete-btn" onclick="deleteLesson('{{ $lesson['id'] }}')">Удалить</button>
                                            </div>
                                        </div>
                                    @endif
                                
                            </li>
                        @endif
                    @endfor
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
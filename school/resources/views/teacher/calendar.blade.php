@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/calendar.css'])
@endsection

@if(isset($data['isAdmin']) && $data['isAdmin'])
    @include('admin.layouts.adminNav')
@else
@include('teacher.nav')
@endif

<div class="container">
    <main class="content">
        @if(isset($data['isAdmin']) && $data['isAdmin'])
            <div class="admin-header" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <h2 style="margin: 0; color: #333;">Календарь преподавателя: {{ $data['selectedTeacher'] }}</h2>
                <a href="{{ route('admin.teacher.profile', $data['user']->id) }}" class="action-btn" style="background: #2563eb; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">Назад к профилю</a>
            </div>
        @endif
        
        <div class="calendar-container">
            <div class="calendar-header">
                <h2>@if(isset($data['isAdmin']) && $data['isAdmin'])Календарь преподавателя{{ $data['selectedTeacher'] }}@elseКалендарь занятий@endif</h2>
                <div class="calendar-controls">
                    <form method="POST" action="{{ route('teacher.calendar.prev.week') }}">
                        @csrf
                        @if(isset($data['isAdmin']) && $data['isAdmin'])
                            <input type="hidden" name="teacher_id" value="{{ $data['user']->id }}">
                        @endif
                        <button name="prevWeek">&lt;</button>
                    </form>
                    <span class="currentMonth">
                        <?php echo date('d.m', strtotime(session('monday'))) . ' - ' . date('d.m', strtotime(session('sunday'))); ?>
                    </span>
                    <form method="POST" action="{{ route('teacher.calendar.next.week') }}">
                        @csrf
                        @if(isset($data['isAdmin']) && $data['isAdmin'])
                            <input type="hidden" name="teacher_id" value="{{ $data['user']->id }}">
                        @endif
                        <button name="nextWeek">&gt;</button>
                    </form>
                </div>
            </div>
            <div class="schedule-filters">
                <form method="GET" action="{{ route('teacher.calendar') }}" class="filters-form">
                    @if(isset($data['isAdmin']) && $data['isAdmin'])
                        <input type="hidden" name="teacher_id" value="{{ $data['user']->id }}">
                    @endif
                    <div class="select-group">
                        <div class="schedule-filter">
                            <select name="group" onchange="this.form.submit()">
                                <option value="">Все группы</option>
                                @foreach ($data['groups'] as $group)
                                    <option value="{{ is_object($group) ? $group->name : $group['name'] }}" {{ $data['selectedGroup'] === (is_object($group) ? $group->name : $group['name']) ? 'selected' : '' }}>
                                        {{ is_object($group) ? $group->name : $group['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="schedule-filter">
                            <select name="subject" onchange="this.form.submit()">
                                <option value="">Все предметы</option>
                                @foreach ($data['subjects'] as $subject)
                                    <option value="{{ is_object($subject) ? $subject->name : $subject }}" {{ $data['selectedSubject'] === (is_object($subject) ? $subject->name : $subject) ? 'selected' : '' }}>
                                        {{ is_object($subject) ? $subject->name : $subject }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

            </div>
            <div class="calendar">
                <div class="grid-container-calendar">
                    <!-- Временные и дневные заголовки -->
                    <div class="time-header" style="grid-column: 1; grid-row: 1;"></div>
                    @php $weekdays = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье']; @endphp
                    @foreach ($weekdays as $i => $day)
                        <div class="weekday" style="grid-column: {{ $i+2 }}; grid-row: 1;">
                            <p>{{ $day }}</p>
                        </div>
                    @endforeach
                    @php $times = ['8:00', '9:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00']; @endphp
                    @foreach ($times as $i => $time)
                        <div class="time" style="grid-column: 1; grid-row: {{ $i+2 }};">
                            <p>{{ $time }}</p>
                        </div>
                    @endforeach
                    <!-- Ячейки расписания -->
                    @php
                        // Для предотвращения дублирования уроков
                        $rendered = [];
                    @endphp
                    @for ($hour = 8; $hour <= 22; $hour++)
                        @for ($day = 1; $day <= 7; $day++)
                            @php $lesson = $data['schedule'][$day][$hour]; @endphp
                            @if ($lesson)
                                @php
                                    $lessonId = md5($lesson['subject'].$lesson['name_group'].$lesson['start_time'].$lesson['end_time'].$lesson['date_']);
                                    $isFirstHour = $hour == (int)date('G', strtotime($lesson['start_time']));
                                    $start = strtotime($lesson['start_time']);
                                    $end = strtotime($lesson['end_time']);
                                    $duration = $lesson ? ceil(($end - $start) / 3600) : 1;
                                @endphp
                                @if ($isFirstHour && !in_array($lessonId, $rendered))
                                    <div class="cell has-lesson"
                                         style="grid-column: {{ $day + 1 }}; grid-row: {{ $hour - 6 }} / span {{ $duration }};
                                                background-color: #131936; color: var(--btn-primary:); border-radius: 10px; position:relative;">
                                        <div style="padding:6px 8px;">
                                            <div style="font-weight:600;">{{ $lesson['subject'] }}</div>
                                            <div style="font-size:13px;color:#374151;">{{ $lesson['name_group'] }}</div>                                        </div>
                                    </div>
                                    @php $rendered[] = $lessonId; @endphp
                                @endif
                            @else
                                <div class="cell" style="grid-column: {{ $day + 1 }}; grid-row: {{ $hour - 6 }};"></div>
                            @endif
                        @endfor
                    @endfor
                </div>
            </div>
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
        @media (max-width: 600px) {
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
@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/students.css'])
<style>
/* Стили для горизонтальной прокрутки таблиц */
.table-scroll-container {
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-width: thin;
    scrollbar-color: var(--border-color) transparent;
    max-width: none !important;
    width: auto !important;
    min-width: 0 !important;
}

/* Убираем ограничения ширины у родительского контейнера */
.container-method {
    max-width: none !important;
    overflow: visible !important;
    width: auto !important;
}

/* Убираем ограничения у table-container */
.table-container {
    max-width: none !important;
    width: auto !important;
    overflow: visible !important;
}

.table-scroll-container::-webkit-scrollbar {
    height: 8px;
}

.table-scroll-container::-webkit-scrollbar-track {
    background: transparent;
}

.table-scroll-container::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 4px;
}

.table-scroll-container::-webkit-scrollbar-thumb:hover {
    background: var(--text-secondary);
}

/* Выравнивание ячеек */
.container-method .table-scroll-container .table-container table th,
.container-method .table-scroll-container .table-container table td {
    text-align: left !important;
}
</style>
@endsection

@if(isset($isAdmin) && $isAdmin)
    @include('admin.layouts.adminNav')
@else
    @include('teacher.nav')
@endif

<div class="container">
    <main class="content">
        @if(isset($isAdmin) && $isAdmin)
            <div class="admin-header" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <h2 style="margin: 0; color: #333;">Студенты преподавателя: {{ $teacher->fio }}</h2>
                <a href="{{ route('admin.teacher.profile', $teacher->users_id) }}" class="action-btn" style="background: #2563eb; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">Назад к профилю</a>
            </div>
        @endif
        
        <div class="students-container">
            <div class="students-header">
                <h2>@if(isset($isAdmin) && $isAdmin)Студенты преподавателя{{ $teacher->fio }}@elseМои студенты@endif</h2>
            </div>
            <div class="students-filters">
                <form method="GET" action="{{ route('teacher.students') }}" class="filters-form">
                    @if(isset($isAdmin) && $isAdmin)
                        <input type="hidden" name="teacher_id" value="{{ $teacher->users_id }}">
                    @endif
                    <select name="group">
                        <option value="">Все группы</option>
                        @foreach ($allGroups as $group)
                            <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>{{ $group }}</option>
                        @endforeach
                    </select>
                    <button type="submit">Применить фильтр</button>
                    @if(request('group'))
                        <a href="{{ route('teacher.students') }}@if(isset($isAdmin) && $isAdmin)?teacher_id={{ $teacher->users_id }}@endif" class="reset-filters">Сбросить фильтр</a>
                    @endif
                </form>
            </div>
            <div class="table-scroll-container">
                <div class="students-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ФИО</th>
                                <th>Группа</th>
                                <th>Средний балл</th>
                                <th>Посещаемость</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($students as $student)
                            <tr>
                                <td>{{ $student->fio }}</td>
                                <td>{{ $student->group_name }}</td>
                                <td>{{ $student->average_performance }}</td>
                                <td>{{ $student->average_attendance }}</td>
                                <td><span class="status active">Активный</span></td>
                                <td>
                                    <button class="view-profile" onclick="window.location.href='{{ route('teacher.studentProfile', $student->id) }}'">Профиль</button>
                                    <button class="schedule-btn" onclick="window.location.href='{{ route('teacher.calendar', ['group' => $student->group_name]) }}'">Расписание</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;">Студенты не найдены</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('wheel', (event) => {
    const target = event.target;
    const tableScrollContainer = target.closest('.table-scroll-container');
    if (tableScrollContainer) {
        event.preventDefault();
        const scrollAmount = 300;
        if (event.deltaY > 0) {
            tableScrollContainer.scrollLeft += scrollAmount;
        } else {
            tableScrollContainer.scrollLeft -= scrollAmount;
        }
    }
}, { passive: false });
</script> 
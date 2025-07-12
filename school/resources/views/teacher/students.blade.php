@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/students.css'])
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
    </main>
</div> 
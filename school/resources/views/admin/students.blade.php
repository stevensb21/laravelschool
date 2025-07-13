@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/students.css'])
@vite(['resources/js/app.js'])



@include('admin.layouts.adminNav')

<div class="container">
    <main class="content">
        <div class="students-container">
            <div class="students-header">
                <h2>Управление студентами</h2>
                
                <button class="add-student-btn" onclick="openModal('addStudentModal')">Добавить студента</button>
            </div>
            <div class="students-filters">
                <form method="GET" action="{{ route('student') }}" class="filters-form">
                    <input type="text" name="fio" placeholder="Поиск студента..."  value="{{ request('fio') }}">
                    <select name="group">
                        <option value="">Все группы</option>
                        @foreach ($allGroups as $group)
                            <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>
                                {{$group}}
                            </option>
                        @endforeach
                    </select>
                    <div class="filters-actions">
                        <button type="submit">Применить фильтры</button>
                        @if(request('fio') || request('group'))
                            <a href="{{ route('student') }}" class="reset-filters">Сбросить фильтры</a>
                        @endif
                    </div>
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
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student->fio; ?></td>
                            <td><?php echo $student->group_name; ?></td>
                            <td><?php echo $student->average_performance; ?></td>
                            <td><?php echo $student->average_attendance; ?></td>
                            <td><span class="status active">Активный</span></td>
                            <td>
                            <button class="edit-btn" onclick="openEditStudnetModal(<?= htmlspecialchars(json_encode([
                                'users_id' => $student->users_id,
                                'username' => $student->user ? $student->user->name : 'Неизвестно', 
                                'fio' => $student->fio,
                                'email' => $student->email,
                                'numberphone' => $student->numberphone,
                                'femaleparent' => $student->femaleparent,
                                'numberparent' => $student->numberparent,
                                'group_name' => $student->group_name,
                                'datebirthday' => $student->datebirthday,
                                'achievements' => implode("\n", is_array($student->achievements) ? $student->achievements : explode(',', trim($student->achievements, '{}')))
                            ])) ?>)">Редактировать</button>
                            <button class="view-profile" onclick="window.location.href='{{ route('admin.student.view', $student->id) }}'">Профиль</button>
                            <button class="schedule-btn" onclick="window.location.href='{{ route('calendar', ['group' => $student['group_name']]) }}'">Расписание</button>
                            <button class="delete-btn" onclick="openDeleteStudentModal(<?= htmlspecialchars(json_encode([
                                'users_id' => $student->users_id,
                                'fio' => $student->fio
                            ])) ?>)">Удалить</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <button class="prev-page">&lt;</button>
                <span class="page-number">1</span>
                <button class="next-page">&gt;</button>
            </div>
        </div>
    </main>
</div>

    @include('admin.layouts.addStudent')
    @include('admin.layouts.editStudent')
    @include('admin.layouts.deleteStudent')

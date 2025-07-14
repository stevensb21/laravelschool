@extends('admin.layouts.head')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/management.css'])
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
    /* Стилизация контейнера Select2 */
    .select2-container--default .select2-selection--multiple {
        min-height: 44px;
        border-radius: 8px;
        border: 1.5px solid #bfc9d1;
        background: #f8fafc;
        font-size: 1rem;
        padding: 4px 8px;
        transition: border-color 0.2s;
    }
    .select2-container--default .select2-selection--multiple:focus,
    .select2-container--default .select2-selection--multiple.select2-selection--focus {
        border-color: #2563eb;
        background: #fff;
        box-shadow: 0 0 0 2px #2563eb22;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 6px;
        margin: 2px 4px 2px 0;
        padding: 4px 10px;
        font-size: 0.98em;
        font-weight: 500;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 6px;
        font-weight: bold;
    }
    .select2-container--default .select2-selection--multiple .select2-search__field {
        background: transparent;
        font-size: 1em;
        color: #222;
    }
    .select2-container--default .select2-selection--multiple .select2-search__field:focus {
        outline: none;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb;
        color: #fff;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #e0e7ff;
        color: #222;
    }
    .select2-container {
        width: 100% !important;
        max-width: 100%;
    }
    .select2-dropdown {
        border-radius: 8px;
        border: 1.5px solid #bfc9d1;
        box-shadow: 0 4px 16px #0001;
        font-size: 1rem;
        z-index: 9999;
    }
    .select2-container--open .select2-dropdown {
        border-color: #2563eb;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        padding: 0 4px 0 0;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
        color: #94a3b8;
        font-size: 1em;
        font-style: italic;
    }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @include('admin.layouts.adminNav')
@endsection

@section('content')
<div class="container">
    <main class="content">
        <div class="management-container">
            <div class="management-header">
                <h2>Управление системой</h2>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="management-grid">
                <div class="management-card">
                    <h3>Управление группами</h3>
                    <div class="card-content">
                        <button class="action-btn" onclick="openCreateGroupModal()">Создать группу</button>
                        <button class="action-btn" onclick="openEditGroupsModal()">Редактировать группы</button>
                        <button class="action-btn" onclick="openDeleteGroupModal()">Удалить группу</button>
                        <div class="groups-list">
                            <h4>Существующие группы ({{ $totalGroups }})</h4>
                            <ul>
                                @foreach($groupsList as $groupName)
                                    <li>{{ $groupName }}</li>
                                @endforeach
                            </ul>
                            <p><strong>Всего студентов в группах:</strong> {{ $totalStudentsInGroups }}</p>
                        </div>
                    </div>
                </div>

                <div class="management-card">
                    <h3>Управление курсами</h3>
                    <div class="card-content">
                        <button class="action-btn" onclick="openCreateCourseModal()">Создать курс</button>
                        <button class="action-btn" onclick="openEditCoursesModal()">Редактировать курсы</button>
                        <button class="action-btn" onclick="openDeleteCourseModal()">Удалить курс</button>
                        <div class="courses-stats">
                            <h4>Статистика курсов</h4>
                            <p>Всего курсов: {{ $totalCourses }}</p>
                            <p>Активных курсов: {{ $activeCourses }}</p>
                            <p>Студентов на курсах: {{ $totalStudentsOnCourses }}</p>
                        </div>
                    </div>
                </div>

                <!-- <div class="management-card">
                    <h3>Настройки системы</h3>
                    <div class="card-content">
                        <button class="action-btn" onclick="openGeneralSettingsModal()">Общие настройки</button>
                        <button class="action-btn" onclick="openSecuritySettingsModal()">Настройки безопасности</button>
                        <button class="action-btn" onclick="openNotificationSettingsModal()">Настройки уведомлений</button>
                        <div class="system-info">
                            <h4>Информация о системе</h4>
                            <p>Версия: {{ $systemInfo['version'] }}</p>
                            <p>Последнее обновление: {{ $systemInfo['last_update'] }}</p>
                            <p>Статус: {{ $systemInfo['status'] }}</p>
                        </div>
                    </div>
                </div> -->

                <div class="management-card">
                    <h3>Резервное копирование</h3>
                    <div class="card-content">
                        <button class="action-btn" onclick="createBackup()">Создать резервную копию</button>
                        <button class="action-btn" onclick="openRestoreModal()">Восстановить из резервной копии</button>
                        <button class="action-btn" onclick="openBackupSettingsModal()">Настройка автоматического бэкапа</button>
                        
                        
                        
                        <div class="backup-info">
                            <h4>Информация о резервных копиях</h4>
                            <p>Последний бэкап: {{ $backupInfo['last_backup'] }}</p>
                            <p>Размер: {{ $backupInfo['size'] }}</p>
                            <p>Статус: {{ $backupInfo['status'] }}</p>
                            <p>Всего копий: {{ $backupInfo['total_backups'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="system-logs">
                <h3>Системные логи</h3>
                <div class="logs-container">
                    @foreach($systemLogs as $log)
                        <div class="log-entry">
                            <span class="timestamp">{{ $log['timestamp'] }}</span>
                            <span class="action">{{ $log['action'] }}</span>
                            <span class="user">{{ $log['user'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
    
    <!-- Модальные окна -->
    @include('admin.management.modals.create-group')
    @include('admin.management.modals.edit-group')
    @include('admin.management.modals.delete-group')
    @include('admin.management.modals.create-course')
    @include('admin.management.modals.edit-course')
    @include('admin.management.modals.delete-course')
    @include('admin.management.modals.restore-backup')

    <script>
    window.allCourses = @json($courses->map(fn($c) => ['id' => (string)$c->id, 'name' => $c->name]));
    window.loadGroupData = function() {
        const groupName = document.getElementById('edit_group_id').value;
        if (!groupName) return;
        fetch(`/management/get-group-data?group_name=${encodeURIComponent(groupName)}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_group_name').value = data.name;
                document.getElementById('edit_group_size').value = data.size;
                let selectedIds = [];
                if (Array.isArray(data.courses_json)) {
                    data.courses_json.forEach(val => {
                        if (typeof val === 'string') {
                            val.split(',').forEach(id => selectedIds.push(id.trim()));
                        } else {
                            selectedIds.push(String(val));
                        }
                    });
                }
                const container = document.getElementById('edit_group_courses_container');
                const oldSelect = document.getElementById('edit_group_courses');
                if (oldSelect) {
                    container.removeChild(oldSelect);
                }
                // Создаем новый select
                const select = document.createElement('select');
                select.id = 'edit_group_courses';
                select.name = 'courses[]';
                select.multiple = true;
                (window.allCourses || []).forEach(course => {
                    const option = document.createElement('option');
                    option.value = String(course.id).trim();
                    option.text = course.name;
                    select.appendChild(option);
                });
                container.appendChild(select);
                // Инициализируем Select2 и выставляем значения
                $(select).select2({
                    width: '100%',
                    placeholder: 'Выберите курсы',
                    allowClear: true
                });
                $(select).val(selectedIds.map(id => String(id).trim())).trigger('change');
                // Удаляем возможную пустую опцию
                $(select).find('option[value=""]').remove();
                // Логируем состояние
                let opts = [];
                for (let i = 0; i < select.options.length; i++) {
                    opts.push(`value=${select.options[i].value}, selected=${select.options[i].selected}, text=${select.options[i].text}`);
                }
                console.log('selectedIds: ' + JSON.stringify(selectedIds) + '\noptions: ' + opts.join('\n'));
            });
    }
    function openCreateGroupModal() {
        document.getElementById('createGroupModal').style.display = 'block';
        // Инициализация Select2 для мультиселекта курсов (если ещё не инициализирован)
        const select = document.getElementById('group_courses');
        if (select) {
            // Удаляем предыдущую инициализацию, если была
            if ($(select).hasClass('select2-hidden-accessible')) {
                $(select).select2('destroy');
            }
            $(select).val([]); // Сброс выбранных значений
            $(select).select2({
                width: '100%',
                placeholder: 'Выберите курсы',
                allowClear: true
            });
            // Удаляем возможную пустую опцию
            $(select).find('option[value=""]').remove();
        }
    }
    function openCreateCourseModal() {
        document.getElementById('createCourseModal').style.display = 'block';
        // Группы
        const selectGroups = document.getElementById('access_groups');
        if (selectGroups) {
            if ($(selectGroups).hasClass('select2-hidden-accessible')) {
                $(selectGroups).select2('destroy');
            }
            $(selectGroups).val([]);
            $(selectGroups).select2({
                width: '100%',
                placeholder: 'Выберите группы',
                allowClear: true
            });
            $(selectGroups).find('option[value=""]').remove();
        }
        // Преподаватели
        const selectTeachers = document.getElementById('access_teachers');
        if (selectTeachers) {
            if ($(selectTeachers).hasClass('select2-hidden-accessible')) {
                $(selectTeachers).select2('destroy');
            }
            $(selectTeachers).val([]);
            $(selectTeachers).select2({
                width: '100%',
                placeholder: 'Выберите преподавателей',
                allowClear: true
            });
            $(selectTeachers).find('option[value=""]').remove();
        }
    }
    function openDeleteGroupModal() {
        document.getElementById('deleteGroupModal').style.display = 'block';
    }
    function openDeleteCourseModal() {
        document.getElementById('deleteCourseModal').style.display = 'block';
    }
    
    // Функции для редактирования
    window.openEditGroupsModal = function() {
        document.getElementById('editGroupModal').style.display = 'block';
        window.loadGroupData();
    }
    function openEditCoursesModal() {
        document.getElementById('editCourseModal').style.display = 'block';
        // Группы
        const selectGroups = document.getElementById('edit_access_groups');
        if (selectGroups) {
            if ($(selectGroups).hasClass('select2-hidden-accessible')) {
                $(selectGroups).select2('destroy');
            }
            $(selectGroups).select2({
                width: '100%',
                placeholder: 'Выберите группы',
                allowClear: true
            });
            $(selectGroups).find('option[value=""]').remove();
        }
        // Преподаватели
        const selectTeachers = document.getElementById('edit_access_teachers');
        if (selectTeachers) {
            if ($(selectTeachers).hasClass('select2-hidden-accessible')) {
                $(selectTeachers).select2('destroy');
            }
            $(selectTeachers).select2({
                width: '100%',
                placeholder: 'Выберите преподавателей',
                allowClear: true
            });
            $(selectTeachers).find('option[value=""]').remove();
        }
    }
    
    // Заглушки для нереализованных функций
    function openGeneralSettingsModal() {
        console.log('Функция общих настроек будет реализована позже');
    }
    function openSecuritySettingsModal() {
        console.log('Функция настроек безопасности будет реализована позже');
    }
    function openNotificationSettingsModal() {
        console.log('Функция настроек уведомлений будет реализована позже');
    }
    function openRestoreModal() {
        document.getElementById('restoreBackupModal').style.display = 'block';
        loadBackupsList();
    }
    
    function loadBackupsList() {
        fetch('/management/backups-list', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayBackupsList(data.backups);
            } else {
                document.getElementById('backupsList').innerHTML = '<p>Ошибка загрузки списка резервных копий</p>';
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки списка резервных копий:', error);
            document.getElementById('backupsList').innerHTML = '<p>Ошибка загрузки списка резервных копий</p>';
        });
    }
    
    function displayBackupsList(backups) {
        const container = document.getElementById('backupsList');
        
        if (backups.length === 0) {
            container.innerHTML = '<p>Резервные копии не найдены</p>';
            return;
        }
        
        let html = '<div class="backup-items">';
        backups.forEach((backup, index) => {
            const date = new Date(backup.created_at).toLocaleString('ru-RU');
            html += `
                <div class="backup-item">
                    <input type="radio" name="selected_backup" id="backup_${index}" value="${backup.name}">
                    <label for="backup_${index}">
                        <strong>${backup.name}</strong><br>
                        <small>Создано: ${date}</small><br>
                        <small>Размер: ${backup.size}</small><br>
                        <small>Таблиц: ${backup.tables_count}</small>
                    </label>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }
    
    function restoreSelectedBackup() {
        const selectedBackup = document.querySelector('input[name="selected_backup"]:checked');
        
        if (!selectedBackup) {
            console.log('Пожалуйста, выберите резервную копию для восстановления');
            return;
        }
        
        if (confirm('ВНИМАНИЕ! Восстановление из резервной копии заменит все текущие данные. Вы уверены, что хотите продолжить?')) {
            const backupName = selectedBackup.value;
            
            fetch('/management/restore-backup', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ backup_name: backupName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Резервная копия успешно восстановлена! Страница будет перезагружена.');
                    location.reload();
                } else {
                    console.error('Ошибка при восстановлении: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка восстановления:', error);
                console.log('Ошибка при восстановлении резервной копии');
            });
        }
    }
    
    function openBackupSettingsModal() {
        console.log('Функция настроек автоматического бэкапа будет реализована позже');
    }
    
    function createBackup() {
        if (confirm('Создать резервную копию системы?')) {
            fetch('/management/backup', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Резервная копия создана успешно!');
                    location.reload();
                } else {
                    console.error('Ошибка при создании резервной копии: ' + data.message);
                }
            });
        }
    }
    
    // Функции для тестирования бэкапа
    function showCurrentRecords() {
        fetch('/test/show-records')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let message = 'Текущие записи в базе данных:\n\n';
                message += 'Группы: ' + (data.data.groups.length > 0 ? data.data.groups.join(', ') : 'нет') + '\n';
                message += 'Курсы: ' + (data.data.courses.length > 0 ? data.data.courses.join(', ') : 'нет') + '\n';
                message += 'Количество студентов: ' + data.data.students_count;
                console.log(message);
            } else {
                console.error('Ошибка: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            console.log('Ошибка при получении данных');
        });
    }
    
    function deleteTestRecords() {
        if (confirm('Удалить тестовые записи? Это действие нельзя отменить!')) {
            fetch('/test/delete-records')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let message = 'Тестовые записи удалены:\n';
                    message += 'Групп: ' + data.deleted.groups + '\n';
                    message += 'Курсов: ' + data.deleted.courses + '\n';
                    message += 'Студентов: ' + data.deleted.students;
                    console.log(message);
                } else {
                    console.error('Ошибка: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                console.log('Ошибка при удалении записей');
            });
        }
    }
    
    function restoreTestData() {
        if (confirm('Восстановить тестовые данные?')) {
            fetch('/test/restore-test-data')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Тестовые данные восстановлены успешно!');
                } else {
                    console.error('Ошибка: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                console.log('Ошибка при восстановлении данных');
            });
        }
    }
    
    // Закрытие модальных окон
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    // Закрытие при клике вне модального окна
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.querySelector('button.action-btn[onclick*="openEditGroupsModal"]');
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                window.openEditGroupsModal();
            });
        }
    });

    window.loadCourseData = function() {
        const courseId = document.getElementById('edit_course_id').value;
        if (!courseId) return;
        fetch(`/management/get-course-data?course_id=${courseId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_course_name').value = data.name;
                // Группы
                const selectGroups = document.getElementById('edit_access_groups');
                if (selectGroups) {
                    for (let option of selectGroups.options) {
                        option.selected = data.groups.includes(option.value);
                    }
                    if ($(selectGroups).hasClass('select2-hidden-accessible')) {
                        $(selectGroups).trigger('change');
                    }
                }
                // Преподаватели - теперь данные приходят в правильном формате
                const selectTeachers = document.getElementById('edit_access_teachers');
                if (selectTeachers) {
                    for (let option of selectTeachers.options) {
                        option.selected = data.teachers.includes(option.value);
                    }
                    if ($(selectTeachers).hasClass('select2-hidden-accessible')) {
                        $(selectTeachers).trigger('change');
                    }
                }
            });
    }
    </script>
</div>
@endsection
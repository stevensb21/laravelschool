@extends('admin.layouts.head')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/management.css'])
    @vite(['resources/css/colors.css'])
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
    /* Стилизация контейнера Select2 */
    .select2-container--default .select2-selection--multiple {
        min-height: 44px;
        border-radius: 8px;
        border: 1.5px solid #bfc9d1;
        background: var(--input-bg);
        font-size: 1rem;
        padding: 4px 8px;
        transition: border-color 0.2s;
    }
    .select2-container--default .select2-selection--multiple:focus,
    .select2-container--default .select2-selection--multiple.select2-selection--focus {
        
        background: #fff;
        box-shadow: 0 0 0 2px #2563eb22;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
     
        color: #fff;
        border: none;
        border-radius: 6px;
        margin: 2px 4px 2px 0;
        padding: 4px 10px;
        font-size: 0.98em;
        background: var(--btn-primary);
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
        <div class="management-container" style="background:var(--card-bg);border-radius:12px;box-shadow:0 2px 8px var(--card-shadow);padding:24px;">
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
                        <button class="action-btn" onclick="window.location.href='{{ route('admin.groups.list') }}'">Просмотр студентов групп</button>
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
    
    <!-- Модальные окна вынесены за пределы .container -->
    @include('admin.management.modals.create-group')
    @include('admin.management.modals.edit-group')
    @include('admin.management.modals.delete-group')
    @include('admin.management.modals.create-course')
    @include('admin.management.modals.edit-course')
    @include('admin.management.modals.delete-course')
    @include('admin.management.modals.restore-backup')

    <!-- Модальное окно подтверждения удаления бэкапа -->
    <div id="confirmDeleteBackupModal" class="modal" style="display:none;z-index:2001;">
        <div class="modal-content" style="max-width:400px;text-align:center;position:relative;top:20px;">
            <span class="close" onclick="closeModal('confirmDeleteBackupModal')">&times;</span>
            <h3 style="margin-bottom:20px;">Подтвердите удаление</h3>
            <div id="confirmDeleteBackupText" style="margin-bottom:30px;"></div>
            <div class="form-actions" style="justify-content:center;gap:16px;">
                <button type="button" class="btn btn-danger" id="confirmDeleteBackupBtn" style="min-width:100px;">Удалить</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('confirmDeleteBackupModal')" style="min-width:100px;">Отмена</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно настройки автоматического бэкапа -->
    <div id="autoBackupSettingsModal" class="modal" style="display:none;z-index:2001;">
        <div class="modal-content" style="max-width:400px;text-align:center;position:relative;top:20px;background:var(--modal-bg);color:var(--text-primary);box-shadow:0 4px 24px var(--card-shadow);border-radius:12px;">
            <span class="close" onclick="closeModal('autoBackupSettingsModal')" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
            <h3 style="margin-bottom:20px;color:var(--text-primary);">Настройка автоматического бэкапа</h3>
            <form id="autoBackupSettingsForm" autocomplete="off">
                <div style="margin-bottom:18px;text-align:left;">
                    <label style="display:flex;align-items:center;gap:10px;color:var(--text-primary);">
                        <input type="checkbox" id="autoBackupEnabled" style="accent-color:var(--btn-primary);"> Включить автосоздание бэкапов
                    </label>
                </div>
                <div style="margin-bottom:18px;text-align:left;">
                    <label for="autoBackupPeriod" style="color:var(--text-primary);">Периодичность:</label><br>
                    <select id="autoBackupPeriod" style="width:100%;margin-top:5px;padding:8px 10px;border-radius:6px;border:1px solid var(--input-border);background:var(--input-bg);color:var(--text-primary);">
                        <option value="daily">Ежедневно</option>
                        <option value="weekly">Еженедельно</option>
                        <option value="monthly">Ежемесячно</option>
                    </select>
                </div>
                <div style="margin-bottom:18px;text-align:left;">
                    <label for="autoBackupTime" style="color:var(--text-primary);">Время запуска:</label><br>
                    <input type="time" id="autoBackupTime" value="03:00" style="width:100%;margin-top:5px;padding:8px 10px;border-radius:6px;border:1px solid var(--input-border);background:var(--input-bg);color:var(--text-primary);">
                </div>
                <div class="form-actions" style="justify-content:center;gap:16px;">
                    <button type="button" class="btn btn-primary" style="background:var(--btn-primary);color:var(--text-light);border-radius:6px;padding:10px 24px;font-weight:500;" onclick="saveAutoBackupSettings()">Сохранить</button>
                    <button type="button" class="btn btn-secondary" style="background:var(--btn-secondary);color:var(--text-light);border-radius:6px;padding:10px 24px;font-weight:500;" onclick="closeModal('autoBackupSettingsModal')">Отмена</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    window.allCourses = @json($courses->map(fn($c) => ['id' => (string)$c->id, 'name' => $c->name]));
    window.loadGroupData = function() {
        const groupName = document.getElementById('edit_group_id').value;
        if (!groupName) return;
        fetch(`/management/get-group-data?group_name=${encodeURIComponent(groupName)}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_group_name').value = data.name;
                // Преподаватель
                const teacherSelect = document.getElementById('edit_group_teacher');
                if (teacherSelect) {
                    for (let i = 0; i < teacherSelect.options.length; i++) {
                        teacherSelect.options[i].selected = (teacherSelect.options[i].value === String(data.teacher_id));
                    }
                }
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
        document.getElementById('createGroupModal').style.display = 'flex';
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
        document.getElementById('createCourseModal').style.display = 'flex';
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
        document.getElementById('deleteGroupModal').style.display = 'flex';
    }
    function openDeleteCourseModal() {
        document.getElementById('deleteCourseModal').style.display = 'flex';
    }
    
    // Функции для редактирования
    function openEditCoursesModal() {
        document.getElementById('editCourseModal').style.display = 'flex';
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
                <div class="backup-item" style="display:flex;align-items:center;justify-content:space-between;gap:10px;position:relative;">
                    <div style="flex:1;">
                    <input type="radio" name="selected_backup" id="backup_${index}" value="${backup.name}">
                    <label for="backup_${index}">
                        <strong>${backup.name}</strong><br>
                        <small>Создано: ${date}</small><br>
                        <small>Размер: ${backup.size}</small><br>
                        <small>Таблиц: ${backup.tables_count}</small>
                    </label>
                    </div>
                    ${index !== 0 ? `<span title='Удалить' style='cursor:pointer;font-size:22px;color:var(--danger-color);user-select:none;margin-left:10px;line-height:1;' onclick="deleteBackupByName('${encodeURIComponent(backup.name)}', this)">&times;</span>` : ''}
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }

    let pendingDeleteBackup = null;
    function deleteBackupByName(backupName, btn) {
        backupName = decodeURIComponent(backupName);
        pendingDeleteBackup = { name: backupName, btn: btn };
        document.getElementById('confirmDeleteBackupText').innerHTML = `Вы уверены, что хотите удалить резервную копию <b>${backupName}</b>? Это действие необратимо!`;
        document.getElementById('confirmDeleteBackupModal').style.display = 'flex';
        // Назначаем обработчик каждый раз при открытии
        var confirmBtn = document.getElementById('confirmDeleteBackupBtn');
        if (confirmBtn) {
            confirmBtn.onclick = function() {
                if (!pendingDeleteBackup) return;
                const { name, btn } = pendingDeleteBackup;
                btn.disabled = true;
                fetch('/management/delete-backup', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ backup_name: name })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal('confirmDeleteBackupModal');
                        alert('Резервная копия успешно удалена!');
                        loadBackupsList();
                    } else {
                        alert('Ошибка при удалении: ' + (data.message || 'Неизвестная ошибка'));
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    alert('Ошибка при удалении резервной копии: ' + error);
                    btn.disabled = false;
                });
                pendingDeleteBackup = null;
            };
            
        }
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
                openEditGroupsModal();
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
    function deleteSelectedBackup() {
        const selectedBackup = document.querySelector('input[name="selected_backup"]:checked');
        if (!selectedBackup) {
            alert('Пожалуйста, выберите резервную копию для удаления');
            return;
        }
        if (!confirm('Вы уверены, что хотите удалить выбранную резервную копию? Это действие необратимо!')) {
            return;
        }
        const backupName = selectedBackup.value;
        fetch('/management/delete-backup', {
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
                alert('Резервная копия успешно удалена!');
                loadBackupsList();
            } else {
                alert('Ошибка при удалении: ' + (data.message || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            alert('Ошибка при удалении резервной копии: ' + error);
        });
    }
    function openEditGroupsModal() {
        document.getElementById('editGroupModal').style.display = 'flex';
        setTimeout(function() {
            window.loadGroupData();
        }, 0);
    }
    function openRestoreModal() {
        document.getElementById('restoreBackupModal').style.display = 'flex';
        loadBackupsList();
    }
    function openBackupSettingsModal() {
        // Получить текущие настройки с сервера (AJAX)
        fetch('/management/auto-backup-settings', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('autoBackupEnabled').checked = !!data.enabled;
            document.getElementById('autoBackupPeriod').value = data.period || 'daily';
            document.getElementById('autoBackupTime').value = data.time || '03:00';
            document.getElementById('autoBackupSettingsModal').style.display = 'flex';
        })
        .catch(() => {
            // Если ошибка — открыть с дефолтными значениями
            document.getElementById('autoBackupEnabled').checked = false;
            document.getElementById('autoBackupPeriod').value = 'daily';
            document.getElementById('autoBackupTime').value = '03:00';
            document.getElementById('autoBackupSettingsModal').style.display = 'flex';
        });
    }
    function saveAutoBackupSettings() {
        const enabled = document.getElementById('autoBackupEnabled').checked;
        const period = document.getElementById('autoBackupPeriod').value;
        const time = document.getElementById('autoBackupTime').value;
        fetch('/management/auto-backup-settings', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ enabled, period, time })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Настройки автосоздания бэкапов сохранены!');
                closeModal('autoBackupSettingsModal');
            } else {
                alert('Ошибка: ' + (data.message || 'Не удалось сохранить настройки.'));
            }
        })
        .catch(() => {
            alert('Ошибка при сохранении настроек.');
            });
    }
    </script>
</div>
@endsection
@extends('admin.layouts.head')
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@vite(['resources/css/students.css'])
@vite(['resources/css/appeals.css'])
<style>
.appeal-form {
    background: var(--card-bg);
    padding: 32px 24px 24px 24px;
    border-radius: 14px;
    box-shadow: 0 2px 8px var(--card-shadow);
    max-width: 600px;
    margin: 0 auto 32px auto;
}
.appeal-form label {
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 6px;
    display: block;
}
.appeal-form input[type="text"],
.appeal-form select,
.appeal-form textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1.5px solid var(--input-border);
    border-radius: 7px;
    font-size: 1rem;
    margin-bottom: 16px;
    background: var(--input-bg);
    color: var(--text-primary);
    transition: border 0.2s;
}
.appeal-form input[type="text"]:focus,
.appeal-form select:focus,
.appeal-form textarea:focus {
    border-color: var(--input-focus);
    outline: none;
}
.appeal-form button {
    background: var(--btn-primary);
    color: var(--text-light);
    padding: 10px 28px;
    border: none;
    border-radius: 7px;
    font-size: 1.08rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.18s;
    margin-top: 8px;
    display: block;
    margin-left: auto;
    margin-right: auto;
}
.appeal-form button:hover {
    background: var(--btn-primary-hover);
}

/* Стили для кнопок действий */
.view-btn, .rate-btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    margin-right: 5px;
    transition: background-color 0.3s;
}

.view-btn {
    background-color: var(--btn-secondary);
    color: var(--text-light);
}

.view-btn:hover {
    background-color: var(--btn-secondary-hover);
}

.rate-btn {
    background-color: var(--success-color);
    color: var(--text-light);
}

.rate-btn:hover {
    background-color: var(--status-active);
}

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
.students-container {
    max-width: none !important;
    overflow: visible !important;
    width: auto !important;
}

/* Убираем ограничения у students-table */
.students-table {
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

/* Принудительная ширина таблицы для тестирования прокрутки */
.students-container .table-scroll-container .students-table table {
    min-width: auto !important;
    width: 99% !important;
}

.students-container .table-scroll-container .students-table table th,
.students-container .table-scroll-container .students-table table td {
    min-width: auto !important;
    white-space: normal !important;
    text-align: left !important;
}

/* Стили для звездочек рейтинга */
.rating-stars {
    display: flex;
    gap: 8px;
    margin-top: 10px;
    flex-direction: row;
}

.rating-stars input[type="radio"] {
    display: none;
}

.rating-stars label {
    font-size: 2.2rem;
    color: var(--accent-color); /* невыбранные — оранжевые */
    text-shadow: 0 1px 2px var(--border-dark), 0 0 1px #fff;
    cursor: pointer;
    transition: color 0.2s, text-shadow 0.2s;
    user-select: none;
}
.rating-stars label.selected,
.rating-stars label.hovered {
    color: var(--status-pending); /* выбранные — жёлтые */
    text-shadow: 0 2px 6px var(--secondary-color), 0 0 2px #fff;
}

/* Стили для toast уведомлений */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 5px;
    color: var(--text-light);
    font-weight: 500;
    z-index: 10000;
    display: none;
}

.toast-success {
    background-color: var(--toast-success);
}

.toast-error {
    background-color: var(--toast-error);
}

.toast-info {
    background-color: var(--toast-info);
}

/* Стили для вкладок */
.appeals-tabs {
    display: flex;
    margin-bottom: 20px;
    border-bottom: 2px solid var(--border-light);
}

.tab-btn {
    padding: 12px 24px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary); 
    border-bottom: 2px solid transparent;
    transition: all 0.3s;

}

.tab-btn:hover {
    color: var(--text-primary);
}

.tab-btn.active {
    color: var(--btn-primary);
    border-bottom-color: var(--btn-primary);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Стили для кнопки ответа */
.reply-btn {
    background-color: var(--btn-primary);
    color: var(--text-light);
}

.reply-btn:hover {
    background-color: var(--btn-primary-hover);
    opacity: 0.85;
}


</style>
@endsection


    @include('student.nav')
    <div class="container" style="flex:1;min-width:0;width:100%;">
        <main class="content">
            <div class="students-container" style="background:var(--card-bg);border-radius:12px;box-shadow:0 2px 8px var(--card-shadow);padding:24px;">
                <div class="students-header">
                    <h2>Обращения</h2>
                </div>
                @if(session('success'))
                    <div style="color:green;margin-bottom:16px;">{{ session('success') }}</div>
                @endif
                <!-- Форма отправки обращения -->
                <form method="POST" action="{{ route('student.appeals.send') }}" class="appeal-form">
                    @csrf
                    <div>
                        <label>Кому:</label>
                        <select name="recipient_id" required>
                            <optgroup label="Администраторы">
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Преподаватели группы">
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->users_id }}">{{ $teacher->fio }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label>Тема:</label>
                        <input type="text" name="subject" required>
                    </div>
                    <div>
                        <label>Сообщение:</label>
                        <textarea name="comment" required style="min-height:90px;"></textarea>
                    </div>
                    <button type="submit">Отправить обращение</button>
                </form>
                <!-- Таблица обращений -->
                @if($appeals->isEmpty())
                    <div style="text-align:center;padding:40px;color:#718096;">
                        <p>У вас пока нет обращений.</p>
                    </div>
                @else
                    <!-- Вкладки -->
                    <div class="appeals-tabs">
                        <button class="tab-btn active" onclick="showTab('sent')">Отправленные обращения</button>
                        <button class="tab-btn" onclick="showTab('received')">Полученные обращения</button>
                    </div>
                    
                    <!-- Отправленные обращения -->
                    <div id="sent-tab" class="tab-content active">
                        @if($sentAppeals->isEmpty())
                            <div style="text-align:center;padding:40px;color:#718096;">
                                <p>У вас пока нет отправленных обращений.</p>
                            </div>
                        @else
                            <div class="table-scroll-container">
                                <div class="students-table">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Дата</th>
                                                <th>Тема</th>
                                                <th>Тип</th>
                                                <th>Статус</th>
                                                <th>Кому</th>
                                                <th>Действия</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sentAppeals as $appeal)
                                                <tr class="{{ $appeal->status === 'Завершено' ? 'completed' : 'active' }}">
                                                    <td>{{ $appeal->created_at->format('d.m.Y') }}</td>
                                                    <td>
                                                        <div class="appeal-title" onclick="openViewModal({{ $appeal->id }})">
                                                            {{ $appeal->title ?? '—' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="type-badge type-{{ strtolower($appeal->type) }}">
                                                            {{ $appeal->type }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge status-{{ strtolower($appeal->status) }}">
                                                            {{ $appeal->status ?? '—' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($appeal->recipient)
                                                            @if($appeal->recipient->student)
                                                                {{ $appeal->recipient->student->fio }}
                                                            @elseif($appeal->recipient->teacher)
                                                                {{ $appeal->recipient->teacher->fio }}
                                                            @else
                                                                {{ $appeal->recipient->name }}
                                                            @endif
                                                        @else
                                                            Неизвестно
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button onclick="openViewModal({{ $appeal->id }})" class="view-btn">Просмотр</button>
                                                        @if($appeal->feedback && !$appeal->like_feedback)
                                                            <button onclick="openRateModal({{ $appeal->id }})" class="rate-btn">Оценить</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Полученные обращения -->
                    <div id="received-tab" class="tab-content">
                        @if($receivedAppeals->isEmpty())
                            <div style="text-align:center;padding:40px;color:#718096;">
                                <p>У вас пока нет полученных обращений.</p>
                            </div>
                        @else
                            <div class="table-scroll-container">
                                <div class="students-table">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Дата</th>
                                                <th>Тема</th>
                                                <th>Тип</th>
                                                <th>Статус</th>
                                                <th>От кого</th>
                                                <th>Действия</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($receivedAppeals as $appeal)
                                                <tr class="{{ $appeal->status === 'Завершено' ? 'completed' : 'active' }}">
                                                    <td>{{ $appeal->created_at->format('d.m.Y') }}</td>
                                                    <td>
                                                        <div class="appeal-title" onclick="openViewModal({{ $appeal->id }})">
                                                            {{ $appeal->title ?? '—' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="type-badge type-{{ strtolower($appeal->type) }}">
                                                            {{ $appeal->type }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge status-{{ strtolower($appeal->status) }}">
                                                            {{ $appeal->status ?? '—' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($appeal->sender)
                                                            @if($appeal->sender->student)
                                                                {{ $appeal->sender->student->fio }}
                                                            @elseif($appeal->sender->teacher)
                                                                {{ $appeal->sender->teacher->fio }}
                                                            @else
                                                                {{ $appeal->sender->name }}
                                                            @endif
                                                        @else
                                                            Неизвестно
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button onclick="openViewModal({{ $appeal->id }})" class="view-btn">Просмотр</button>
                                                        @if(!$appeal->feedback)
                                                            <button onclick="openReplyModal({{ $appeal->id }})" class="reply-btn">Ответить</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </main>
    </div>

<!-- Модальное окно просмотра обращения -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeViewModal()" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
        <div id="viewContent">
            <!-- Содержимое будет загружено динамически -->
        </div>
    </div>
</div>

<!-- Модальное окно оценки ответа -->
<div id="rateModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeRateModal()" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
        <h3>Оценить ответ</h3>
        <form id="rateForm">
            <input type="hidden" id="rateAppealId" name="appeal_id">
            <div class="form-group">
                <label>Оценка ответа:</label>
                <div class="rating-stars" id="starRating">
                    <label data-value="1">★</label>
                    <label data-value="2">★</label>
                    <label data-value="3">★</label>
                    <label data-value="4">★</label>
                    <label data-value="5">★</label>
                    <input type="hidden" name="like_feedback" id="starRatingValue" value="0">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="save-btn">Оценить</button>
                <button type="button" class="cancel-btn" onclick="closeRateModal()">Отмена</button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно ответа на обращение -->
<div id="replyModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeReplyModal()" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
        <h3>Ответить на обращение</h3>
        <form id="replyForm">
            <input type="hidden" id="replyAppealId" name="appeal_id">
            <div class="form-group">
                <label for="feedback">Ответ:</label>
                <textarea id="feedback" name="feedback" rows="4" required maxlength="1000" placeholder="Введите ваш ответ..."></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="save-btn">Отправить ответ</button>
                <button type="button" class="cancel-btn" onclick="closeReplyModal()">Отмена</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast уведомление -->
<div id="toast"></div>

<script>
// Ожидаем полной загрузки DOM
document.addEventListener('DOMContentLoaded', function() {

    
    function showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = `toast toast-${type}`;
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);
    }

    function openViewModal(appealId) {
        fetch(`/appeals/${appealId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('viewContent').innerHTML = data.html;
                    document.getElementById('viewModal').style.display = 'flex';
                } else {
                    showToast('Ошибка при загрузке обращения', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Произошла ошибка при загрузке обращения', 'error');
            });
    }

    function closeViewModal() {
        document.getElementById('viewModal').style.display = 'none';
    }

    function openRateModal(appealId) {
        document.getElementById('rateAppealId').value = appealId;
        document.getElementById('rateModal').style.display = 'flex';
    }

    function closeRateModal() {
        document.getElementById('rateModal').style.display = 'none';
    }

    function openReplyModal(appealId) {
        document.getElementById('replyAppealId').value = appealId;
        document.getElementById('replyModal').style.display = 'flex';
    }

    function closeReplyModal() {
        document.getElementById('replyModal').style.display = 'none';
    }

    // Функция переключения вкладок
    function showTab(tabName) {
        // Скрываем все вкладки
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.classList.remove('active');
        });
        
        // Убираем активный класс у всех кнопок
        const tabBtns = document.querySelectorAll('.tab-btn');
        tabBtns.forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Показываем нужную вкладку
        document.getElementById(tabName + '-tab').classList.add('active');
        
        // Активируем нужную кнопку
        event.target.classList.add('active');
    }

    // Делаем функции глобальными
    window.openViewModal = openViewModal;
    window.closeViewModal = closeViewModal;
    window.openRateModal = openRateModal;
    window.closeRateModal = closeRateModal;
    window.openReplyModal = openReplyModal;
    window.closeReplyModal = closeReplyModal;
    window.showTab = showTab;

    // Обработчики форм
    document.getElementById('rateForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const appealId = formData.get('appeal_id');
        formData.append('_method', 'PUT');
        
        fetch(`/appeals/${appealId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeRateModal();
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Ошибка при оценке: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Произошла ошибка при оценке', 'error');
        });
    };

    document.getElementById('replyForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const appealId = formData.get('appeal_id');
        formData.append('_method', 'PUT');
        
        fetch(`/student/appeals/${appealId}/reply`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeReplyModal();
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Ошибка при ответе: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Произошла ошибка при ответе', 'error');
        });
    };

    // Закрытие модальных окон при клике вне их
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }

    // Звёздный рейтинг: выбор слева направо
    const starLabels = document.querySelectorAll('#starRating label');
    const starInput = document.getElementById('starRatingValue');
    starLabels.forEach((label, idx) => {
        label.addEventListener('mouseenter', () => {
            starLabels.forEach((l, i) => l.classList.toggle('hovered', i <= idx));
        });
        label.addEventListener('mouseleave', () => {
            starLabels.forEach(l => l.classList.remove('hovered'));
        });
        label.addEventListener('click', () => {
            starInput.value = idx + 1;
            starLabels.forEach((l, i) => l.classList.toggle('selected', i <= idx));
        });
    });

    // Обработчик колесика мыши для таблиц
    document.addEventListener('wheel', (event) => {
    
        
        // Проверяем, находится ли курсор над таблицей или её контейнером
        const target = event.target;
        const tableScrollContainer = target.closest('.table-scroll-container');
        
        if (tableScrollContainer) {
    
            
            // Предотвращаем вертикальную прокрутку страницы
            event.preventDefault();
            
            // Определяем направление прокрутки
            const scrollAmount = 300; // Увеличиваем шаг прокрутки
            if (event.deltaY > 0) {
                tableScrollContainer.scrollLeft += scrollAmount;
            
            } else {
                tableScrollContainer.scrollLeft -= scrollAmount;
       
            }
        }
    }, { passive: false });

    
});
</script>


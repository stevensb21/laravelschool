@extends('admin.layouts.head')
@section('head')

<meta name="csrf-token" content="{{ csrf_token() }}">
@vite(['resources/css/homework.css'])
<style>
.table-scroll-container {
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-width: thin;
    scrollbar-color: var(--border-color) transparent;
    max-width: none !important;
    width: auto !important;
    min-width: 0 !important;
}
.table-scroll-container::-webkit-scrollbar { height: 8px; }
.table-scroll-container::-webkit-scrollbar-track { background: transparent; }
.table-scroll-container::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }
.table-scroll-container::-webkit-scrollbar-thumb:hover { background: var(--text-secondary); }
.submissions-table .table-scroll-container table th,
.submissions-table .table-scroll-container table td {
    text-align: left !important;
}
</style>
@include('teacher.nav')

<div class="container">
    <main class="content">
        <div class="homework-container">
            <div class="homework-header">
                <h2>Работы студентов: {{ $homework->course->name ?? 'Не указан' }}</h2>
                <a href="{{ route('teacher.homework') }}" class="back-btn">← Назад к заданиям</a>
            </div>
            
            <div class="homework-info">
                <p><strong>Группа:</strong> {{ $homework->group->name ?? 'Не указана' }}</p>
                <p><strong>Преподаватель:</strong> {{ $homework->teacher->fio ?? 'Не указан' }}</p>
                <p><strong>Срок сдачи:</strong> {{ $homework->deadline }}</p>
                <p><strong>Описание:</strong> {{ $homework->description ?? 'Описание отсутствует' }}</p>
            </div>

            <div class="table-scroll-container">
                <div class="submissions-table">
                    <table>
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>Студент</th>
                                <th>Статус</th>
                                <th>Файл</th>
                                <th>Оценка</th>
                                <th>Комментарий</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentsList as $index => $item)
                                <tr class="{{ $item['hasSubmitted'] ? 'submitted' : 'not-submitted' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item['student']->fio }}</td>
                                    <td>
                                        @if($item['hasSubmitted'])
                                            <span class="status submitted">Сдано</span>
                                        @else
                                            <span class="status not-submitted">Не сдано</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['hasSubmitted'] && $item['filePath'])
                                            <a href="{{ $item['filePath'] }}" target="_blank" class="file-link" download>
                                                📄 Скачать файл
                                            </a>
                                        @else
                                            <span class="no-file">Файл не загружен</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['hasSubmitted'])
                                            <span class="grade {{ $item['grade'] ? 'has-grade' : 'no-grade' }}">
                                                {{ $item['grade'] ?? 'Не оценено' }}
                                            </span>
                                        @else
                                            <span class="no-grade">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['hasSubmitted'])
                                            <span class="feedback">
                                                {{ $item['feedback'] ?? 'Нет комментария' }}
                                            </span>
                                        @else
                                            <span class="no-feedback">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['hasSubmitted'])
                                            <button class="grade-btn" onclick="openGradeModal({{ $item['submission']->id }}, '{{ $item['student']->fio }}', {{ $item['grade'] ?? 'null' }}, '{{ $item['feedback'] ?? '' }}')">
                                                Оценить
                                            </button>
                                        @else
                                            <span class="no-action">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Модальное окно для оценки -->
<div id="gradeModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('gradeModal')" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
        <h3>Оценка работы</h3>
        <form id="gradeForm">
            <input type="hidden" id="submissionId" name="submission_id">
            <div class="form-group">
                <label for="studentName">Студент:</label>
                <input type="text" id="studentName" readonly>
            </div>
            <div class="form-group">
                <label for="grade">Оценка:</label>
                <select id="grade" name="grade" required>
                    <option value="">Выберите оценку</option>
                    <option value="5">5 (Отлично)</option>
                    <option value="4">4 (Хорошо)</option>
                    <option value="3">3 (Удовлетворительно)</option>
                    <option value="2">2 (Неудовлетворительно)</option>
                </select>
            </div>
            <div class="form-group">
                <label for="feedback">Комментарий:</label>
                <textarea id="feedback" name="feedback" rows="3" placeholder="Введите комментарий к работе..."></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="save-btn">Сохранить</button>
                <button type="button" class="cancel-btn" onclick="closeGradeModal()">Отмена</button>
            </div>
        </form>
    </div>
</div>

<script>
function openGradeModal(submissionId, studentName, currentGrade, currentFeedback) {
    document.getElementById('submissionId').value = submissionId;
    document.getElementById('studentName').value = studentName;
    document.getElementById('grade').value = currentGrade || '';
    document.getElementById('feedback').value = currentFeedback || '';
    document.getElementById('gradeModal').style.display = 'flex';
}

function closeGradeModal() {
    document.getElementById('gradeModal').style.display = 'none';
}

// Закрытие модального окна при клике на X
if(document.querySelector('.close'))
    document.querySelector('.close').onclick = closeGradeModal;

// Закрытие модального окна при клике вне его
window.onclick = function(event) {
    const modal = document.getElementById('gradeModal');
    if (event.target == modal) {
        closeGradeModal();
    }
}

// Обработка отправки формы
if(document.getElementById('gradeForm'))
    document.getElementById('gradeForm').onsubmit = function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/homework/grade', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeGradeModal();
                location.reload(); // Перезагружаем страницу для обновления данных
            } else {
                alert('Ошибка при сохранении оценки: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при сохранении оценки');
        });
    };

// Горизонтальная прокрутка таблиц с колесиком мыши
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

@endsection 
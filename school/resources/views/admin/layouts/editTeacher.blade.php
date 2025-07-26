@vite(['resources/js/app.js'])

<div id="editTeacherModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editTeacherModal')" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
            <h2>Редактировать преподавателя</h2>
            
            @if (isset($error)):
                <div class="error-message">{{ $error }}</div>
            @endif
            
            <form method="POST" action="{{ route('teacher.edit') }}" class="edit-teacher-form">
                @csrf
                <input type="hidden" id="edit_users_id" name="users_id" >
                
                <div class="form-group">
                    <label for="edit_username">Логин:</label>
                    <input type="text" id="edit_username" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_password">Новый пароль (оставьте пустым, чтобы не менять):</label>
                    <input type="password" id="edit_password" name="password">
                </div>
                
                <div class="form-group">
                    <label for="edit_fio">ФИО:</label>
                    <input type="text" id="edit_fio" name="fio" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_job_title">Должность:</label>
                    <input type="text" id="edit_job_title" name="job_title" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email:</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_subjects">Предметы (каждый с новой строки):</label>
                    <textarea id="edit_subjects" name="subjects" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_education">Образование (каждое с новой строки):</label>
                    <textarea id="edit_education" name="education" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_achievements">Достижения (каждое с новой строки):</label>
                    <textarea id="edit_achievements" name="achievements" rows="3"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="edit_teacher" class="submit-btn">Сохранить</button>
                    <button type="button" onclick="closeEditModal()" class="cancel-btn">Отмена</button>
                </div>
            </form>
        </div>
    </div>
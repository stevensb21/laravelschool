@vite(['resources/js/app.js'])



<div id="addTeacherModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addTeacherModal')">&times;</span>
        <h2>Добавить преподавателя</h2>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="{{ route('teacher.store') }}" class="add-teacher-form">
            @csrf

            @if (session('success'))
                <div class="success-message">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="error-message">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="error-message">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="form-group">
                <label for="username">Логин:</label>
                <input type="text" id="username" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="fio">ФИО:</label>
                <input type="text" id="fio" name="fio" required>
            </div>
            
            <div class="form-group">
                <label for="job_title">Должность:</label>
                <input type="text" id="job_title" name="job_title" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="subjects">Предметы (каждый с новой строки):</label>
                <textarea id="subjects" name="subjects" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="education">Образование (каждое с новой строки):</label>
                <textarea id="education" name="education" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="achievements">Достижения (каждое с новой строки):</label>
                <textarea id="achievements" name="achievements" rows="3"></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="add_teacher" class="submit-btn">Добавить</button>
                <button type="button" onclick="closeModal('addTeacherModal')" class="cancel-btn">Отмена</button>
            </div>
        </form>
    </div>
</div>
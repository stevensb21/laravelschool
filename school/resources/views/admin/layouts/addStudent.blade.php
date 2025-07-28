@vite(['resources/js/app.js'])

<div id="addStudentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addStudentModal')" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
            <h2>Добавить студента</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="{{ route('student.add') }}" class="add-teacher-form" id="addStudentForm">
                @csrf
                <div class="form-group">
                    <label for="name">Логин:</label>
                    <input type="text" id="login" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" required>
                    @error('password')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="fio">ФИО:</label>
                    <input type="text" id="fio" name="fio" value="{{ old('fio') }}" required>
                    @error('fio')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="numberphone">Номер телефона:</label>
                    <input type="text" id="numberphone" name="numberphone" value="{{ old('numberphone') }}" required>
                    @error('numberphone')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="femaleparent">ФИО родителя:</label>
                    <input type="text" id="femaleparent" name="femaleparent" value="{{ old('femaleparent') }}" required>
                    @error('femaleparent')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="numberparent">Номер телефона родителя:</label>
                    <input type="text" id="numberparent" name="numberparent" value="{{ old('numberparent') }}" required>
                    @error('numberparent')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="group">Основная группа:</label>
                    <select id="group" name="group" required>
                        <option value="">Выберите основную группу</option>
                        @foreach ($allGroups as $group):
                            <option value="{{ $group }}" {{ old('group') == $group ? 'selected' : '' }}>
                                    {{$group}}
                            </option>
                        @endforeach
                    </select>
                    <small style="color: var(--text-secondary); font-size: 12px; margin-top: 4px; display: block;">
                        Основная группа определяет основные предметы студента. Дополнительные группы можно добавить позже через управление группами.
                    </small>
                    @error('group')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="datebirthday">Дата рождения:</label>
                    <input type="date" id="datebirthday" name="datebirthday" value="{{ old('datebirthday') }}" required max="{{ \Carbon\Carbon::now()->subYear()->format('Y-m-d') }}">
                    @error('datebirthday')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="achievements">Достижения (каждое с новой строки):</label>
                    <textarea id="achievements" name="achievements" rows="3">{{ old('achievements') }}</textarea>
                    @error('achievements')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="add_student" class="submit-btn">Добавить</button>
                    <button type="button" onclick="closeModal('addStudentModal')" class="cancel-btn">Отмена</button>
                </div>
            </form>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    handleAddStudentForm();
});
</script>
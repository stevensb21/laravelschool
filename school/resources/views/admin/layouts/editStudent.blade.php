@vite(['resources/js/app.js'])
<!-- Модальное окно для редактирования студента -->
    <div id="editStudentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editStudentModal')">&times;</span>
            <h2>Редактировать студента</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="/student/edit" class="add-teacher-form">
                @csrf
                <input type="hidden" id="edit_users_id" name="users_id">

                <div class="form-group">
                    <label for="edit_login">Логин:</label>
                    <input type="text" id="edit_login" name="login" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_password">Пароль:</label>
                    <input type="password" id="edit_password" name="password" placeholder="Оставьте пустым, если не хотите менять">
                </div>

                <div class="form-group">
                    <label for="edit_fio">ФИО:</label>
                    <input type="text" id="edit_fio" name="fio" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_numberphone">Номер телефона:</label>
                    <input type="text" id="edit_numberphone" name="numberphone" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email:</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="edit_femaleparent">ФИО родителя:</label>
                    <input type="text" id="edit_femaleparent" name="femaleparent" required>
                </div>

                <div class="form-group">
                    <label for="edit_numberparent">Номер телефона родителя:</label>
                    <input type="text" id="edit_numberparent" name="numberparent" required>
                </div>

                <div class="form-group">
                    <label for="edit_group">Выбор группы:</label>
                    <select id="edit_group" name="group" required>
                        <option value="">Выберите группу</option>
                        @foreach ($allGroups as $group):
                            <option value="{{ $group }}" 
                            {{ request('group') == $group ? 'selected' : '' }}>
                                    {{$group}}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_datebirthday">Дата рождения:</label>
                    <input type="date" id="edit_datebirthday" name="datebirthday" required max="{{ \Carbon\Carbon::now()->subYear()->format('Y-m-d') }}">
                </div>
                
                <div class="form-group">
                    <label for="edit_achievements">Достижения (каждое с новой строки):</label>
                    <textarea id="edit_achievements" name="achievements" rows="3" placeholder=""></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="edit_student" class="submit-btn">Сохранить</button>
                    <button type="button" onclick="closeModal('editStudentModal')" class="cancel-btn">Отмена</button>
                </div>
            </form>
        </div>
    </div>

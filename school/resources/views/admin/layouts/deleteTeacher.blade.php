@vite(['resources/js/app.js'])

 <!-- Модальное окно для подтверждения удаления -->
<div id="deleteTeacherModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('deleteTeacherModal')" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
        <h2>Подтверждение удаления</h2>
        <p id="delete-confirmation-text"></p>
        
        <form method="POST" action="{{ route('teacher.delete') }}" class="delete-teacher-form">
            @csrf
            <input type="hidden" id="delete_users_id" name="users_id">
            
            <div class="form-actions">
                <button type="submit" name="delete_teacher" class="submit-btn delete">Удалить</button>
                <button type="button" onclick="closeDeleteModal()" class="cancel-btn">Отмена</button>
            </div>
        </form>
    </div>
</div>

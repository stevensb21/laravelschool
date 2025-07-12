<div id="deleteGroupModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('deleteGroupModal')">&times;</span>
        <h3>Удалить группу</h3>
        <form action="{{ route('management.deleteGroup') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="group_id">Выберите группу для удаления:</label>
                <select id="group_id" name="group_id" required>
                    <option value="">Выберите группу...</option>
                    @foreach($groupsList as $groupName)
                        <option value="{{ $groupName }}">{{ $groupName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="warning">
                <p><strong>Внимание!</strong> Удаление группы приведет к удалению всех связанных данных.</p>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-danger">Удалить</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteGroupModal')">Отмена</button>
            </div>
        </form>
    </div>
</div> 
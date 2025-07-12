<div id="restoreBackupModal" class="modal">
    <div class="modal-content" style="max-height:80vh;overflow-y:auto;">
        <span class="close" onclick="closeModal('restoreBackupModal')">&times;</span>
        <h3>Восстановить из резервной копии</h3>
        <div class="backup-list">
            <h4>Доступные резервные копии:</h4>
            <div id="backupsList">
                <p>Загрузка списка резервных копий...</p>
            </div>
        </div>
        <div class="warning">
            <p><strong>Внимание!</strong> Восстановление из резервной копии заменит все текущие данные. Убедитесь, что вы сохранили важную информацию.</p>
        </div>
        <div class="form-actions">
            <button type="button" class="btn btn-danger" onclick="restoreSelectedBackup()">Восстановить выбранную копию</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('restoreBackupModal')">Отмена</button>
        </div>
    </div>
</div>

<!-- Красивый скролл для модального окна -->
<style>
.modal-content {
    scrollbar-width: thin;
    scrollbar-color: #2563eb #f8fafc;
}
.modal-content::-webkit-scrollbar {
    width: 8px;
}
.modal-content::-webkit-scrollbar-thumb {
    background: #2563eb;
    border-radius: 4px;
}
.modal-content::-webkit-scrollbar-track {
    background: #f8fafc;
}
</style> 
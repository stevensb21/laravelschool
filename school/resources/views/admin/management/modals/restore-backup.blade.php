<div id="restoreBackupModal" class="modal">
    <div class="modal-content" style="max-height:80vh;overflow-y:auto;">
        <span class="close" onclick="closeModal('restoreBackupModal')" style="color:var(--error-color);font-size:22px;position:absolute;right:18px;top:12px;cursor:pointer;">&times;</span>
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

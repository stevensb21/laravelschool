import './bootstrap';
import { openModal, 
    closeModal, 
    openEditModal, 
    closeEditModal,
    openDeleteModal,
    closeDeleteModal,
    openEditStudnetModal,
    openDeleteStudentModal,
    closeDeleteStudentModal,
    handleAddStudentForm

} from './modalWindow';

// Делаем функции доступными глобально
window.openModal = openModal;
window.closeModal = closeModal;
window.openEditModal = openEditModal;
window.closeEditModal = closeEditModal;
window.openDeleteModal = openDeleteModal;
window.closeDeleteModal = closeDeleteModal;
window.openEditStudnetModal = openEditStudnetModal;
window.openDeleteStudentModal = openDeleteStudentModal;
window.closeDeleteStudentModal = closeDeleteStudentModal;
window.handleAddStudentForm = handleAddStudentForm;
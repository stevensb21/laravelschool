// Экспортируем функции
    export function openModal(str) {
        document.getElementById(str).style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    export function closeModal(str) {
        document.getElementById(str).style.display = 'none';
        document.body.style.overflow = '';
    }

    export function openEditStudnetModal(studentData) {
            // Получаем логин пользователя по users_id
            
                    // Заполняем поля формы данными студента
                    document.getElementById('edit_users_id').value = studentData.users_id;
                    document.getElementById('edit_login').value = studentData.username;
                    document.getElementById('edit_password').value = '';
                    document.getElementById('edit_fio').value = studentData.fio;
                    document.getElementById('edit_datebirthday').value = studentData.datebirthday;
                    document.getElementById('edit_email').value = studentData.email;
                    document.getElementById('edit_numberphone').value = studentData.numberphone;
                    document.getElementById('edit_femaleparent').value = studentData.femaleparent;
                    document.getElementById('edit_numberparent').value = studentData.numberparent;
                   document.getElementById('edit_group').value = studentData.group_name;
                   document.getElementById('edit_achievements').value = studentData.achievements;
                    
                    // Показываем модальное окно
                    document.getElementById('editStudentModal').style.display = 'block';
               
        }

    
    export function openEditModal(teacherData) {
        // Получаем логин пользователя по users_id
       
                
                // Заполняем остальные поля формы
                document.getElementById('edit_users_id').value = teacherData.users_id;
                document.getElementById('edit_username').value = teacherData.name;
                document.getElementById('edit_password').value = '';
                document.getElementById('edit_fio').value = teacherData.fio;
                document.getElementById('edit_job_title').value = teacherData.job_title;
                document.getElementById('edit_email').value = teacherData.email;
                document.getElementById('edit_subjects').value = teacherData.subjects;
                document.getElementById('edit_education').value = teacherData.education;
                document.getElementById('edit_achievements').value = teacherData.achievements;
                
                // Показываем модальное окно
                document.getElementById('editTeacherModal').style.display = 'block';
            
    }

    export function closeEditModal() {
        document.getElementById('editTeacherModal').style.display = 'none';
    }

    export function openDeleteModal(teacherData) {
        document.getElementById('delete_users_id').value = teacherData.users_id;
        document.getElementById('delete-confirmation-text').textContent = 
            `Вы действительно хотите удалить преподавателя ${teacherData.fio}? Это действие нельзя будет отменить.`;
        document.getElementById('deleteTeacherModal').style.display = 'block';
    }

    export function openDeleteStudentModal(StudentData) {
        document.getElementById('delete_users_id').value = StudentData.users_id;
        document.getElementById('delete-confirmation-text').textContent = 
            `Вы действительно хотите удалить студента ${StudentData.fio}? Это действие нельзя будет отменить.`;
        document.getElementById('deleteStudentModal').style.display = 'block';
    }

    export function closeDeleteModal() {
        document.getElementById('deleteTeacherModal').style.display = 'none';
    }

    export function closeDeleteStudentModal() {
        document.getElementById('deleteStudentModal').style.display = 'none';
    }

    // Функция для обработки отправки формы добавления студента
    export function handleAddStudentForm() {
        const form = document.getElementById('addStudentForm');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Успешно - закрываем модальное окно и перезагружаем страницу
                    closeModal('addStudentModal');
                    window.location.reload();
                } else {
                    // Ошибки валидации - показываем их
                    clearErrors();
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('error');
                                const errorSpan = input.parentNode.querySelector('.error-text');
                                if (errorSpan) {
                                    errorSpan.textContent = data.errors[field][0];
                                }
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }

    // Функция для очистки ошибок
    function clearErrors() {
        const form = document.getElementById('addStudentForm');
        if (!form) return;
        
        form.querySelectorAll('.error-text').forEach(span => {
            span.textContent = '';
        });
        
        form.querySelectorAll('input, textarea, select').forEach(input => {
            input.classList.remove('error');
        });
    }

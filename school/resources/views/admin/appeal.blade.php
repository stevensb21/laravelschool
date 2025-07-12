@extends('admin.layouts.head')
@section('head')

@vite(['resources/css/appeal.css'])
@include('admin.layouts.adminNav')

<div class="container">
        <main class="content">
            <div class="appeals-container">
                <div class="appeals-header">
                    <h2>Управление обращениями</h2>
                    <div class="appeals-stats">
                        <span class="stat-item">Новые: 5</span>
                        <span class="stat-item">В обработке: 3</span>
                        <span class="stat-item">Решено: 12</span>
                    </div>
                </div>
                <div class="appeals-filters">
                    <input type="text" placeholder="Поиск обращений...">
                    <select>
                        <option value="">Все статусы</option>
                        <option value="new">Новые</option>
                        <option value="in-progress">В обработке</option>
                        <option value="resolved">Решено</option>
                    </select>
                    <select>
                        <option value="">Все типы</option>
                        <option value="technical">Технические</option>
                        <option value="academic">Учебные</option>
                        <option value="other">Прочие</option>
                    </select>
                    <select>
                        <option value="">Все отправители</option>
                        <option value="student">Студенты</option>
                        <option value="teacher">Преподаватели</option>
                    </select>
                </div>
                <div class="appeals-list">
                    <div class="appeal-item">
                        <div class="appeal-header">
                            <h3>Вопрос по домашнему заданию</h3>
                            <span class="status in-progress">В обработке</span>
                        </div>
                        <div class="appeal-details">
                            <p><strong>От:</strong> Иванов И.И. (Студент)</p>
                            <p><strong>Кому:</strong> Петров А.С. (Преподаватель)</p>
                            <p><strong>Дата:</strong> 15.03.2024</p>
                            <p><strong>Тип:</strong> Учебный</p>
                            <p><strong>Сообщение:</strong> Здравствуйте, у меня возник вопрос по решению квадратных уравнений...</p>
                        </div>
                        <div class="appeal-actions">
                            <button class="view-appeal">Просмотр</button>
                            <button class="assign-btn">Назначить ответственного</button>
                            <button class="close-appeal">Закрыть</button>
                        </div>
                    </div>
                    <div class="appeal-item">
                        <div class="appeal-header">
                            <h3>Техническая проблема</h3>
                            <span class="status new">Новое</span>
                        </div>
                        <div class="appeal-details">
                            <p><strong>От:</strong> Петрова А.С. (Студент)</p>
                            <p><strong>Кому:</strong> Администратор</p>
                            <p><strong>Дата:</strong> 16.03.2024</p>
                            <p><strong>Тип:</strong> Технический</p>
                            <p><strong>Сообщение:</strong> Не могу загрузить файл с домашним заданием...</p>
                        </div>
                        <div class="appeal-actions">
                            <button class="view-appeal">Просмотр</button>
                            <button class="assign-btn">Назначить ответственного</button>
                            <button class="close-appeal">Закрыть</button>
                        </div>
                    </div>
                    <div class="appeal-item">
                        <div class="appeal-header">
                            <h3>Вопрос по расписанию</h3>
                            <span class="status resolved">Решено</span>
                        </div>
                        <div class="appeal-details">
                            <p><strong>От:</strong> Сидорова М.В. (Преподаватель)</p>
                            <p><strong>Кому:</strong> Администратор</p>
                            <p><strong>Дата:</strong> 14.03.2024</p>
                            <p><strong>Тип:</strong> Прочий</p>
                            <p><strong>Сообщение:</strong> Прошу уточнить расписание на следующую неделю...</p>
                            <p><strong>Ответ:</strong> Расписание обновлено в системе.</p>
                        </div>
                        <div class="appeal-actions">
                            <button class="view-appeal">Просмотр</button>
                            <button class="reopen-btn">Открыть повторно</button>
                        </div>
                    </div>
                </div>
                <div class="pagination">
                    <button class="prev-page">&lt;</button>
                    <span class="page-number">1</span>
                    <button class="next-page">&gt;</button>
                </div>
            </div>
        </main>
    </div>
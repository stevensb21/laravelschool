<nav id="teacherSidebar" style="background:var(--nav-bg);color:var(--nav-text);">
    <div class="mobile-nav-header">
        <span class="mobile-logo">okapi-board</span>
        <button class="mobile-nav-toggle" id="teacherNavToggle" aria-label="Открыть меню">
            <span class="mobile-nav-icon"></span>
        </button>
    </div>
    <div class="sidebar-links" id="teacherSidebarLinks">
        <div class="photoNav"><img src="{{ asset('images/man.jpg') }}" alt="Картинка"></div>
        <a href="{{ route('teacher.account') }}">Личный кабинет</a>
        <a href="{{ route('teacher.calendar') }}">Календарь</a>
        <a href="{{ route('teacher.students') }}">Студенты</a>
        <a href="{{ route('teacher.lesson') }}">Урок</a>
        <a href="{{ route('teacher.methodology') }}">Методпакеты</a>
        <a href="{{ route('teacher.homework') }}">Домашнее задание</a>
        <a href="{{ route('teacher.appeals') }}">Обращения</a>
        <a href="{{ route('chats.index') }}">Чаты</a>
        <a href="{{ route('teacher.reviews') }}">Отзывы</a>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="exist">Выйти</button>
        </form>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('teacherNavToggle');
        const links = document.getElementById('teacherSidebarLinks');
        toggle.addEventListener('click', function() {
            links.classList.toggle('open');
        });
    });
</script> 
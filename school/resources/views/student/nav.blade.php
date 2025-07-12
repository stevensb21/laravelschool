<nav class="sidebar" id="studentSidebar">
    <div class="mobile-nav-header">
        <span class="mobile-logo">okapi-board</span>
        <button class="mobile-nav-toggle" id="studentNavToggle" aria-label="Открыть меню">
            <span class="mobile-nav-icon"></span>
        </button>
    </div>
    <div class="sidebar-links" id="studentSidebarLinks">
        <div class="photoNav"><img src="{{ asset('images/man.png') }}" alt="Картинка"></div>
        <a href="{{ route('student.account') }}">Личный кабинет</a>
        <a href="{{ route('student.calendar') }}">Календарь</a>
        <a href="{{ route('student.homework') }}">Домашние задания</a>
        <a href="{{ route('student.grades') }}">Оценки</a>
        <a href="{{ route('student.attendance') }}">Посещаемость</a>
        <a href="{{ route('student.appeals') }}">Обращения</a>
        <a href="{{ route('student.reviews') }}">Отзывы</a>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="exist">Выйти</button>
        </form>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('studentNavToggle');
        const links = document.getElementById('studentSidebarLinks');
        toggle.addEventListener('click', function() {
            links.classList.toggle('open');
        });
    });
</script> 
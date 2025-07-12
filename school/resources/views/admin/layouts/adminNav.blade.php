<nav class="sidebar" id="adminSidebar">
    <div class="mobile-nav-header">
        <span class="mobile-logo">okapi-board</span>
        <button class="mobile-nav-toggle" id="adminNavToggle" aria-label="Открыть меню">
            <span class="mobile-nav-icon"></span>
        </button>
    </div>
    <div class="sidebar-links" id="adminSidebarLinks">
        <a href="{{route('account.index')}}">Личный кабинет</a>
        <a href="{{route('calendar')}}">Календарь</a>
        <a href="{{route('teacher')}}" class="{{ request()->routeIs('teacher') ? 'active' : '' }}">Преподаватели</a>
        <a href="{{route('student')}}">Студенты</a>
        <a href="{{route('method')}}">Методология</a>
        <a href="{{route('homework')}}">Домашняя работа</a>
        <a href="{{route('appeals')}}">Обращения</a>
        <a href="{{route('admin.reviews.index')}}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">Модерация отзывов</a>
        <a href="{{route('statistic')}}">Статистика</a>
        <a href="{{route('management')}}">Управление</a>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="exist">Выйти</button>
        </form>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('adminNavToggle');
        const links = document.getElementById('adminSidebarLinks');
        toggle.addEventListener('click', function() {
            links.classList.toggle('open');
        });
    });
</script>
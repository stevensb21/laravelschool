<nav class="student-nav" style="min-width:220px;max-width:260px;background:#f8fafc;padding:24px 0 24px 0;border-radius:12px;box-shadow:0 2px 8px #0001;">
    <ul style="list-style:none;padding:0;margin:0;">
        <li style="margin-bottom:18px;"><a href="{{ route('student.account') }}" class="student-nav-link">Личный кабинет</a></li>
        <li style="margin-bottom:18px;"><a href="{{ route('student.calendar') }}" class="student-nav-link">Календарь</a></li>
        <li style="margin-bottom:18px;"><a href="{{ route('student.homework') }}" class="student-nav-link">Домашние задания</a></li>
        <li style="margin-bottom:18px;"><a href="{{ route('student.grades') }}" class="student-nav-link">Оценки</a></li>
        <li style="margin-bottom:18px;"><a href="{{ route('student.attendance') }}" class="student-nav-link">Посещаемость</a></li>
        <li><a href="{{ route('student.appeals') }}" class="student-nav-link">Обращения</a></li>
    </ul>
</nav>
<style>
.student-nav-link {
    display: block;
    padding: 10px 24px;
    color: #374151;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    transition: background 0.15s, color 0.15s;
}
.student-nav-link:hover, .student-nav-link.active {
    background: #2563eb;
    color: #fff;
}
</style> 
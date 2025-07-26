@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/students.css'])
<style>
.students-table table {
    width: 100%;
    border-collapse: collapse;
}
.students-table th, .students-table td {
    text-align: center;
    vertical-align: middle;
    padding: 10px 8px;
}
.students-table th {
    font-weight: 600;
    background: var(--bg-secondary);
    color: var(--text-primary);
}
.students-table td {
    color: var(--text-secondary);
}
</style>
@endsection


    @include('student.nav')
    <div class="container">
        <main class="content">
            <div class="students-container" style="background:var(--card-bg);border-radius:12px;box-shadow:0 2px 8px var(--card-shadow);padding:24px;max-width:900px;margin:0 auto;">
                <div class="students-header">
                    <h2>Оценки</h2>
                </div>
                
                @if($grades->isEmpty())
                    <div style="text-align:center;padding:40px;color:var(--text-color);">
                        <p>У вас пока нет оценок.</p>
                    </div>
                @else
                    <!-- Общая статистика -->
                    <div style="background:var(--card-bg);border-radius:12px;box-shadow:0 2px 8px var(--card-shadow);padding:24px;margin-bottom:24px;">
                        <h3 style="font-size:1.3rem;font-weight:600;margin:0 0 16px 0;color:var(--text-primary);">Общая статистика</h3>
                        <div style="display:flex;gap:20px;flex-wrap:wrap;">
                            <div style="flex:1;text-align:center;padding:16px;background:var(--bg-secondary);border-radius:8px;border:2px solid var(--border-color);min-width:180px;">
                                <div style="font-size:1.8rem;font-weight:700;color:var(--info-color);">{{ $average }}</div>
                                <div style="font-size:0.9rem;color:var(--text-secondary);font-weight:500;">Средний балл</div>
                            </div>
                            <div style="flex:1;text-align:center;padding:16px;background:var(--bg-secondary);border-radius:8px;border:2px solid var(--border-color);min-width:180px;">
                                <div style="font-size:1.8rem;font-weight:700;color:var(--warning-color);">{{ $average_exam }}</div>
                                <div style="font-size:0.9rem;color:var(--text-secondary);font-weight:500;">Средний балл за экзамен</div>
                            </div>
                            <div style="flex:1;text-align:center;padding:16px;background:var(--bg-secondary);border-radius:8px;border:2px solid var(--border-color);min-width:180px;">
                                <div style="font-size:1.8rem;font-weight:700;color:var(--success-color);">{{ $attendance }}%</div>
                                <div style="font-size:0.9rem;color:var(--text-secondary);font-weight:500;">Посещаемость</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Статистика оценок -->
                    <div style="background:var(--card-bg);border-radius:12px;box-shadow:0 2px 8px var(--card-shadow);padding:24px;margin-bottom:24px;">
                        <h3 style="font-size:1.3rem;font-weight:600;margin:0 0 16px 0;color:var(--text-primary);">Статистика оценок</h3>
                        <div style="display:flex;gap:16px;flex-wrap:wrap;">
                            <div style="flex:1;text-align:center;padding:16px;background:var(--success-bg);border-radius:8px;border:2px solid var(--success-color);min-width:150px;">
                                <div style="font-size:2rem;font-weight:700;color:var(--success-color);">{{ $gradeStats['fives'] }}</div>
                                <div style="font-size:0.9rem;color:var(--success-color);font-weight:500;">Пятёрок</div>
                            </div>
                            <div style="flex:1;text-align:center;padding:16px;background:var(--info-bg);border-radius:8px;border:2px solid var(--info-color);min-width:150px;">
                                <div style="font-size:2rem;font-weight:700;color:var(--info-color);">{{ $gradeStats['fours'] }}</div>
                                <div style="font-size:0.9rem;color:var(--info-color);font-weight:500;">Четвёрок</div>
                            </div>
                            <div style="flex:1;text-align:center;padding:16px;background:var(--warning-bg);border-radius:8px;border:2px solid var(--warning-color);min-width:150px;">
                                <div style="font-size:2rem;font-weight:700;color:var(--warning-color);">{{ $gradeStats['threes'] }}</div>
                                <div style="font-size:0.9rem;color:var(--warning-color);font-weight:500;">Троек</div>
                            </div>
                            <div style="flex:1;text-align:center;padding:16px;background:var(--danger-bg);border-radius:8px;border:2px solid var(--danger-color);min-width:150px;">
                                <div style="font-size:2rem;font-weight:700;color:var(--danger-color);">{{ $gradeStats['twos'] }}</div>
                                <div style="font-size:0.9rem;color:var(--danger-color);font-weight:500;">Двоек</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="students-table">
                        <table>
                            <thead>
                                <tr>
                                    <th title="Дата выставления оценки" style="color:var(--text-primary);">Дата</th>
                                    <th title="Название предмета" style="color:var(--text-primary);">Предмет</th>
                                    <th title="Тип оценки: урок или домашнее задание" style="color:var(--text-primary);">Тип</th>
                                    <th title="Оценка по 5-балльной шкале" style="color:var(--text-primary);">Оценка</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grades as $grade)
                                    <tr>
                                        <td>{{ $grade->created_at->format('d.m.Y') }}</td>
                                        <td>{{ $grade->subject ?? '—' }}</td>
                                        <td>{{ $grade->grade_type ?? '—' }}</td>
                                        <td style="font-size:1rem;">{{ $grade->grade_lesson > 0 ? $grade->grade_lesson : ($grade->homework > 0 ? $grade->homework : '—') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </main>
    </div>

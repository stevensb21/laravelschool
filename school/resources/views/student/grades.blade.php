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
    background: #f7fafc;
}
</style>
@endsection


    @include('student.nav')
    <div class="container">
        <main class="content">
            <div class="students-container">
                <div class="students-header">
                    <h2>Оценки</h2>
                </div>
                
                @if($grades->isEmpty())
                    <div style="text-align:center;padding:40px;color:#718096;">
                        <p>У вас пока нет оценок.</p>
                    </div>
                @else
                    <!-- Общая статистика -->
                    <div style="background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.04);padding:24px;margin-bottom:24px;">
                        <h3 style="font-size:1.3rem;font-weight:600;margin:0 0 16px 0;color:#2d3748;">Общая статистика</h3>
                        <div style="display:flex;gap:20px;flex-wrap:wrap;">
                            <div style="flex:1;text-align:center;padding:16px;background:#f7fafc;border-radius:8px;border:2px solid #e2e8f0;min-width:180px;">
                                <div style="font-size:1.8rem;font-weight:700;color:#2b6cb0;">{{ $average }}</div>
                                <div style="font-size:0.9rem;color:#4a5568;font-weight:500;">Средний балл</div>
                            </div>
                            <div style="flex:1;text-align:center;padding:16px;background:#f7fafc;border-radius:8px;border:2px solid #e2e8f0;min-width:180px;">
                                <div style="font-size:1.8rem;font-weight:700;color:#c05621;">{{ $average_exam }}</div>
                                <div style="font-size:0.9rem;color:#4a5568;font-weight:500;">Средний балл за экзамен</div>
                            </div>
                            <div style="flex:1;text-align:center;padding:16px;background:#f7fafc;border-radius:8px;border:2px solid #e2e8f0;min-width:180px;">
                                <div style="font-size:1.8rem;font-weight:700;color:#2f855a;">{{ $attendance }}%</div>
                                <div style="font-size:0.9rem;color:#4a5568;font-weight:500;">Посещаемость</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Статистика оценок -->
                    <div style="background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.04);padding:24px;margin-bottom:24px;">
                        <h3 style="font-size:1.3rem;font-weight:600;margin:0 0 16px 0;color:#2d3748;">Статистика оценок</h3>
                        <div style="display:flex;gap:16px;flex-wrap:wrap;">
                            <div style="flex:1;text-align:center;padding:16px;background:#e6f7e6;border-radius:8px;border:2px solid #68d391;min-width:150px;">
                                <div style="font-size:2rem;font-weight:700;color:#2f855a;">{{ $gradeStats['fives'] }}</div>
                                <div style="font-size:0.9rem;color:#2f855a;font-weight:500;">Пятёрок</div>
                            </div>
                            <div style="flex:1;text-align:center;padding:16px;background:#ebf8ff;border-radius:8px;border:2px solid #63b3ed;min-width:150px;">
                                <div style="font-size:2rem;font-weight:700;color:#2b6cb0;">{{ $gradeStats['fours'] }}</div>
                                <div style="font-size:0.9rem;color:#2b6cb0;font-weight:500;">Четвёрок</div>
                            </div>
                            <div style="flex:1;text-align:center;padding:16px;background:#fef5e7;border-radius:8px;border:2px solid #ed8936;min-width:150px;">
                                <div style="font-size:2rem;font-weight:700;color:#c05621;">{{ $gradeStats['threes'] }}</div>
                                <div style="font-size:0.9rem;color:#c05621;font-weight:500;">Троек</div>
                            </div>
                            <div style="flex:1;text-align:center;padding:16px;background:#fed7d7;border-radius:8px;border:2px solid #fc8181;min-width:150px;">
                                <div style="font-size:2rem;font-weight:700;color:#c53030;">{{ $gradeStats['twos'] }}</div>
                                <div style="font-size:0.9rem;color:#c53030;font-weight:500;">Двоек</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="students-table">
                        <table>
                            <thead>
                                <tr>
                                    <th title="Дата выставления оценки" style="color:#2d3748;">Дата</th>
                                    <th title="Название предмета" style="color:#2d3748;">Предмет</th>
                                    <th title="Тип оценки: урок или домашнее задание" style="color:#2d3748;">Тип</th>
                                    <th title="Оценка по 5-балльной шкале" style="color:#2d3748;">Оценка</th>
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

@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/students.css'])
@endsection


    @include('student.nav')
    <div class="container" style="flex:1;min-width:0;">
        <main class="content">
            <div class="students-container">
                <div class="students-header">
                    <h2>Посещаемость</h2>
                </div>
                
                @if($attendance->isEmpty())
                    <div style="text-align:center;padding:40px;color:#718096;">
                        <p>Данные о посещаемости пока не загружены.</p>
                    </div>
                @else
                    <div class="students-filters" style="margin-bottom:20px;">
                        @php
                            $total = $attendance->count();
                            $present = $attendance->where('attendance', true)->count();
                            $absent = $total - $present;
                            $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                        @endphp
                        <div class="attendance-stats-center">
                            <div style="background:#f7fafc;padding:15px;border-radius:8px;min-width:150px;text-align:center;">
                                <div style="font-size:24px;font-weight:600;color:#38a169;">{{ $percentage }}%</div>
                                <div style="font-size:14px;color:#718096;">Общая посещаемость</div>
                            </div>
                            <div style="background:#f7fafc;padding:15px;border-radius:8px;min-width:150px;text-align:center;">
                                <div style="font-size:24px;font-weight:600;color:#38a169;">{{ $present }}</div>
                                <div style="font-size:14px;color:#718096;">Присутствовал</div>
                            </div>
                            <div style="background:#f7fafc;padding:15px;border-radius:8px;min-width:150px;text-align:center;">
                                <div style="font-size:24px;font-weight:600;color:#e53e3e;">{{ $absent }}</div>
                                <div style="font-size:14px;color:#718096;">Отсутствовал</div>
                            </div>
                        </div>
                    </div>
                    <style>
                        .attendance-stats-center {
                            display: flex;
                            justify-content: center;
                            gap: 20px;
                            flex-wrap: wrap;
                        }
                        @media (max-width: 600px) {
                            .attendance-stats-center {
                                flex-direction: column;
                                align-items: center;
                                gap: 12px;
                            }
                        }
                    </style>
                    
                    <div class="students-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>Статус</th>
                                    <th>Комментарий</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendance as $record)
                                    <tr>
                                        <td>{{ $record->created_at->format('d.m.Y') }}</td>
                                        <td>
                                            @if($record->attendance)
                                                <span class="status active">Присутствовал</span>
                                            @else
                                                <span class="status inactive">Отсутствовал</span>
                                            @endif
                                        </td>
                                        <td>{{ $record->notes ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </main>
    </div>

@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/admin/homework.css'])
@endsection

@if(isset($isAdmin) && $isAdmin)
    @include('admin.layouts.adminNav')
@else
@include('teacher.nav')
@endif

<div class="content" style="background:#f7fafc;min-height:100vh;margin-left:0;">
    @if(isset($isAdmin) && $isAdmin)
        <div class="admin-header" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; max-width: 600px; margin: 20px auto 0 auto;">
            <h2 style="margin: 0; color: #333;">Уроки преподавателя: {{ $teacher->fio }}</h2>
            <a href="{{ route('admin.teacher.profile', $teacher->users_id) }}" class="action-btn" style="background: #2563eb; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">Назад к профилю</a>
        </div>
    @endif
    
    <div style="max-width:600px;margin:48px auto 0 auto;">
        <h1 style="font-size:2.2rem;font-weight:700;color:#1a202c;margin-bottom:8px;text-align:center;">@if(isset($isAdmin) && $isAdmin)Уроки преподавателя{{ $teacher->fio }}@elseУроки@endif</h1>
        <p style="color:#4a5568;font-size:1.1rem;text-align:center;margin-bottom:32px;">Планирование и проведение уроков</p>
        <div style="background:#fff;border-radius:16px;padding:32px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <h2 style="font-size:1.3rem;font-weight:600;color:#1a202c;margin-bottom:24px;text-align:center;">Сегодняшние уроки</h2>
            @if($lessons->isEmpty())
                <div style="text-align:center;color:#a0aec0;font-size:1.1rem;">Сегодня уроков нет</div>
            @else
                <div style="display:flex;flex-direction:column;gap:20px;">
                    @foreach($lessons as $lesson)
                        <div style="background:#f7fafc;border-radius:10px;padding:20px 24px;box-shadow:0 1px 4px rgba(0,0,0,0.03);">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <div>
                                    <div style="font-size:1.1rem;font-weight:600;">{{ $lesson->subject ?? $lesson->course ?? 'Без предмета' }}</div>
                                    <div style="color:#4a5568;font-size:0.98rem;">Группа: {{ $lesson->name_group ?? '—' }}</div>
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-size:1rem;color:#131936;">{{ $lesson->start_time }} - {{ $lesson->end_time }}</div>
                                    <a href="{{ route('teacher.lesson.students', ['lesson_id' => $lesson->id]) }}"
                                       style="margin-top:8px;display:inline-block;padding:7px 18px;background:var(--btn-primary);color:white;border:none;border-radius:7px;font-size:0.98rem;font-weight:500;text-decoration:none;">
                                        Начать урок
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div> 

<style>
    @media (max-width: 1024px) {
        body {  
            padding-top: 50px;
        }
        
    } 
</style>




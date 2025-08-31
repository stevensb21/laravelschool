@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/homework.css'])
@vite(['resources/css/colors.css'])
@endsection


    @include('student.nav')
    <div class="container">
        <main class="content">
            <div class="students-container" style="background:var(--card-bg);border-radius:12px;box-shadow:0 2px 8px var(--card-shadow);padding:24px;max-width:900px;margin:0 auto;">
                <div class="homework-header" style="margin-bottom:24px;">
                    <h2 style="font-size:2rem;font-weight:700;margin:0 0 8px 0;">Домашние задания</h2>
                </div>
                <div class="homework-list" style="display:flex;flex-direction:column;gap:24px;">
                    @forelse($homeworks as $homework)
                        @php
                            $submission = $homework->homeWorkStudents->first();
                            $fileUrl = \App\Helpers\FileHelper::getFileUrl($homework->file_path);
                        @endphp
                        <div class="homework-item" style="background:var(--bg-secondary);border-radius:12px;padding:24px 20px;box-shadow:0 2px 8px var(--card-shadow);">
                            <div class="homework-header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                                <h3 style="font-size:1.2rem;font-weight:600;margin:0;">{{ $homework->course->name ?? 'Без предмета' }}</h3>
                                <span class="status {{ $submission && $submission->grade ? 'completed' : ($submission ? 'active' : 'overdue') }}" style="padding:4px 12px;border-radius:6px;font-size:0.95rem;font-weight:500;@if($submission && $submission->grade)background:var(--success-bg);color:var(--success-text);@elseif($submission)background:var(--info-bg);color:var(--info-text);@else background:var(--danger-bg);color:var(--danger-text);@endif">
                                    {{ $submission && $submission->grade ? 'Оценено' : ($submission ? 'Сдано' : 'Не сдано') }}
                                </span>
                            </div>
                            <div class="homework-details" style="font-size:1rem;color:var(--text-secondary);">
                                <p style="margin:6px 0;"><strong>Преподаватель:</strong> {{ $homework->teacher->fio ?? 'Не указан' }}</p>
                                <p style="margin:6px 0;"><strong>Срок сдачи:</strong> <span style="color:var(--info-text);">{{ $homework->deadline }}</span></p>
                                <p style="margin:6px 0;"><strong>Описание:</strong> <span style="color:var(--text-light);">{{ $homework->description }}</span></p>
                                <p style="margin:6px 0;">
                                    <strong>Файл задания:</strong>
                                    @if($homework->file_path)
                                        <a href="{{ $fileUrl }}" target="_blank" style="color:var(--primary-color);text-decoration:underline;font-weight:500;">
                                            Скачать
                                        </a>
                                    @else
                                        <span style="color:var(--text-light);">Нет файла</span>
                                    @endif
                                </p>
                                @if($submission)
                                    <p style="margin:6px 0;"><strong>Ваша работа:</strong>
                                                                        @if($submission && $submission->file_path)
                                    <a href="{{ \App\Helpers\FileHelper::getFileUrl($submission->file_path) }}" target="_blank" style="color:var(--info-color);text-decoration:underline;" download>Скачать</a>
                                @else
                                            <span style="color:var(--text-light);">Не загружена</span>
                                        @endif
                                    </p>
                                    <p style="margin:6px 0;"><strong>Оценка:</strong> <span style="color:var(--success-text);font-weight:600;">{{ $submission->grade ?? '—' }}</span></p>
                                    <p style="margin:6px 0;"><strong>Комментарий:</strong> <span style="color:var(--text-light);">{{ $submission->feedback ?? '—' }}</span></p>
                                @else
                                    <form method="POST" action="{{ route('student.homework.submit', $homework->id) }}" enctype="multipart/form-data" style="margin-top:12px;display:flex;gap:12px;align-items:center;">
                                        @csrf
                                        <label style="background:var(--bg-light);padding:8px 16px;border-radius:6px;cursor:pointer;font-size:0.98rem;display:flex;align-items:center;gap:10px;">
                                            <input type="file" name="file" required style="display:none;" onchange="this.parentNode.querySelector('.file-name').innerText = this.files[0]?.name || 'Файл не выбран'">
                                            <span class="file-name" style="color:var(--btn-primary);max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:inline-block;">Файл не выбран</span>
                                            
                                        </label>
                                        <button type="submit" style="background:var(--btn-primary);color:var(--text-light);padding:8px 18px;border:none;border-radius:6px;font-size:1rem;cursor:pointer;transition:background 0.2s;">Сдать работу</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:40px;color:var(--text-color);">
                            <p>Нет домашних заданий для вашей группы.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>

@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/homework.css'])
@vite(['resources/css/colors.css'])
@endsection


    @include('student.nav')
    <div class="container"">
        <main class="content">
            <div class="homework-container" style="background:#fff;border-radius:16px;box-shadow:0 4px 16px rgba(0,0,0,0.07);padding:32px 32px 24px 32px;max-width:600px;margin:0 auto;">
                <div class="homework-header" style="margin-bottom:24px;">
                    <h2 style="font-size:2rem;font-weight:700;margin:0 0 8px 0;">Домашние задания</h2>
                </div>
                <div class="homework-list" style="display:flex;flex-direction:column;gap:24px;">
                    @forelse($homeworks as $homework)
                        @php
                            $submission = $homework->homeWorkStudents->first();
                            $filePath = $homework->file_path;
                            if (strpos($filePath, 'http') === 0) {
                                $fileUrl = $filePath;
                            } elseif (strpos($filePath, '/storage/') === 0) {
                                $fileUrl = asset(ltrim($filePath, '/'));
                            } elseif (strpos($filePath, 'storage/') === 0) {
                                $fileUrl = asset($filePath);
                            } else {
                                $fileUrl = asset('storage/' . ltrim($filePath, '/'));
                            }
                        @endphp
                        <div class="homework-item" style="background:#f7fafc;border-radius:12px;padding:24px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                            <div class="homework-header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                                <h3 style="font-size:1.2rem;font-weight:600;margin:0;">{{ $homework->course->name ?? 'Без предмета' }}</h3>
                                <span class="status {{ $submission && $submission->grade ? 'completed' : ($submission ? 'active' : 'overdue') }}" style="padding:4px 12px;border-radius:6px;font-size:0.95rem;font-weight:500;@if($submission && $submission->grade)background:#e6f7e6;color:#2f855a;@elseif($submission)background:#ebf8ff;color:#2b6cb0;@else background:#fed7d7;color:#c53030;@endif">
                                    {{ $submission && $submission->grade ? 'Оценено' : ($submission ? 'Сдано' : 'Не сдано') }}
                                </span>
                            </div>
                            <div class="homework-details" style="font-size:1rem;color:#4a5568;">
                                <p style="margin:6px 0;"><strong>Преподаватель:</strong> {{ $homework->teacher->fio ?? 'Не указан' }}</p>
                                <p style="margin:6px 0;"><strong>Срок сдачи:</strong> <span style="color:#2b6cb0;">{{ $homework->deadline }}</span></p>
                                <p style="margin:6px 0;"><strong>Описание:</strong> <span style="color:#718096;">{{ $homework->description }}</span></p>
                                <p style="margin:6px 0;">
                                    <strong>Файл задания:</strong>
                                    @if($homework->file_path)
                                        <a href="{{ asset('storage/' . ltrim($homework->file_path, '/')) }}" target="_blank" style="color:#2563eb;text-decoration:underline;font-weight:500;">
                                            Скачать
                                        </a>
                                    @else
                                        <span style="color:#a0aec0;">Нет файла</span>
                                    @endif
                                </p>
                                @if($submission)
                                    <p style="margin:6px 0;"><strong>Ваша работа:</strong>
                                        @if($submission && $submission->file_path)
                                            <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" style="color:#3182ce;text-decoration:underline;" download>Скачать</a>
                                        @else
                                            <span style="color:#a0aec0;">Не загружена</span>
                                        @endif
                                    </p>
                                    <p style="margin:6px 0;"><strong>Оценка:</strong> <span style="color:#2f855a;font-weight:600;">{{ $submission->grade ?? '—' }}</span></p>
                                    <p style="margin:6px 0;"><strong>Комментарий:</strong> <span style="color:#718096;">{{ $submission->feedback ?? '—' }}</span></p>
                                @else
                                    <form method="POST" action="{{ route('student.homework.submit', $homework->id) }}" enctype="multipart/form-data" style="margin-top:12px;display:flex;gap:12px;align-items:center;">
                                        @csrf
                                        <label style="background:#edf2f7;padding:8px 16px;border-radius:6px;cursor:pointer;font-size:0.98rem;display:flex;align-items:center;gap:10px;">
                                            <input type="file" name="file" required style="display:none;" onchange="this.parentNode.querySelector('.file-name').innerText = this.files[0]?.name || 'Файл не выбран'">
                                            <span>Выберите файл</span>
                                            <span class="file-name" style="color:#718096;">Файл не выбран</span>
                                        </label>
                                        <button type="submit" style="background:var(--btn-primary);color:#fff;padding:8px 18px;border:none;border-radius:6px;font-size:1rem;cursor:pointer;transition:background 0.2s;">Сдать работу</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:40px;color:#718096;">
                            <p>Нет домашних заданий для вашей группы.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>

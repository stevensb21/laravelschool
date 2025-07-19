@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/homework.css'])
@endsection

@include('teacher.nav')
<div class="content" style="background:#f7fafc;min-height:100vh;margin-left:0;">
<div style="width:80%;margin:48px auto 0 auto;">
    <div class="container" style="width:100%;padding:32px 0 0 0;">
        <div style="background:none;border-radius:0;box-shadow:none;">
            <div class="page-header" style="margin-bottom:32px;display:flex;flex-wrap:wrap;align-items:center;justify-content:flex-start;gap:16px;padding:0 0 0 0;">
                <div>
                    <h1 style="font-size:2.2rem;font-weight:700;color:#1a202c;margin-bottom:6px;">{{ $lesson->subject ?? 'Урок' }} <span style="color:#a0aec0;font-weight:400;">— {{ $lesson->name_group }}</span></h1>
                    <p style="color:#4a5568;font-size:1.1rem;">{{ $lesson->start_time }} - {{ $lesson->end_time }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('teacher.lesson.attendance', ['lesson_id' => $lesson->id]) }}" id="attendance-form" enctype="multipart/form-data" style="width:100%;">
                @csrf
                <div style="background:#f9fafb;border-radius:10px;padding:28px 20px 20px 20px;box-shadow:none;margin-bottom:32px;width:100%;">
                    <h2 style="font-size:1.25rem;font-weight:600;margin-bottom:18px;">Посещаемость и оценка за урок</h2>
                    <table style="width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;">
                        <thead>
                            <tr style="background:#f1f5f9;">
                                <th style="padding:12px 14px;text-align:left;font-weight:600;color:#374151;">ФИО</th>
                                <th style="padding:12px 14px;text-align:center;font-weight:600;color:#374151;">Пришел</th>
                                <th style="padding:12px 14px;text-align:center;font-weight:600;color:#374151;">Оценка за урок</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:12px 14px;">{{ $student->fio }}</td>
                                <td style="padding:12px 14px;text-align:center;">
                                    <input type="checkbox" name="attendance[{{ $student->id }}]" value="1" checked>
                                </td>
                                <td style="padding:12px 14px;text-align:center;">
                                    <select name="grade[{{ $student->id }}]" style="width:60px;text-align:center;border-radius:6px;border:1px solid #e2e8f0;padding:4px 8px;">
                                        <option value="">—</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="background:#fff;border-radius:16px;padding:36px 28px 32px 28px;box-shadow:0 4px 24px rgba(0,0,0,0.07);margin-bottom:32px;width:100%;max-width:600px;margin-left:auto;margin-right:auto;">
                    <h2 style="font-size:1.35rem;font-weight:700;margin-bottom:22px;letter-spacing:-1px;">Домашнее задание для группы</h2>
                    @if($currentHomework)
                        <div style="background:#f1f5f9;border-radius:8px;padding:14px 16px 12px 16px;margin-bottom:22px;display:flex;align-items:center;gap:12px;">
                            <span style="font-weight:600;color:#2563eb;">Задано:</span>
                            @if($currentHomework->description === 'Из методпакета')
                                <span style="color:#374151;">Из методпакета</span>
                            @elseif($currentHomework->description === 'Загружено преподавателем')
                                <span style="color:#374151;">Файл преподавателя</span>
                            @endif
                            @if($currentHomework->file_path)
                                @php
                                    $filePath = $currentHomework->file_path;
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
                                <a href="{{ $fileUrl }}" target="_blank" style="margin-left:10px;color:#2563eb;text-decoration:underline;font-weight:500;">Открыть файл</a>
                            @endif
                        </div>
                    @endif
                    <div style="margin-bottom:22px;">
                        <label style="font-weight:500;display:block;margin-bottom:8px;">Выбрать из методпакета:</label>
                        <select id="homework_from_method" name="homework_from_method" style="width:100%;margin-top:0;border-radius:8px;border:1.5px solid #e2e8f0;padding:12px 14px;font-size:1.08rem;transition:border 0.2s;outline:none;box-sizing:border-box;">
                            <option value="">— Не выбрано —</option>
                            @foreach($homeworkTitles as $i => $title)
                                <option value="{{ $homeworkFiles[$i] ?? '' }}">{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="margin:18px 0;text-align:center;color:#a0aec0;font-size:1.08rem;font-weight:500;letter-spacing:1px;">или</div>
                    <div style="margin-bottom:24px;">
                        <label style="font-weight:500;display:block;margin-bottom:8px;">Загрузить свой файл:</label>
                        <input type="file" id="homework_file" name="homework_file" style="display:block;width:100%;padding:10px 0;font-size:1.05rem;border-radius:8px;border:1.5px solid #e2e8f0;background:#f9fafb;">
                    </div>
                    <script>
                        const select = document.getElementById('homework_from_method');
                        const fileInput = document.getElementById('homework_file');
                        select.addEventListener('change', function() {
                            if (this.value) {
                                fileInput.disabled = true;
                            } else {
                                fileInput.disabled = false;
                            }
                        });
                        fileInput.addEventListener('change', function() {
                            if (this.value) {
                                select.disabled = true;
                            } else {
                                select.disabled = false;
                            }
                        });
                    </script>
                    <div style="text-align:center;margin-top:18px;">
                        <button type="submit" style="width:100%;max-width:260px;padding:15px 0;background:#2563eb;color:white;border:none;border-radius:10px;font-weight:700;font-size:1.15rem;cursor:pointer;box-shadow:0 2px 8px rgba(37,99,235,0.08);transition:background 0.2s;">Сохранить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div> 
</div> 

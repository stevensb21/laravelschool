@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/method.css'])
@endsection

@if(isset($isAdmin) && $isAdmin)
    @include('admin.layouts.adminNav')
@else
@include('teacher.nav')
@endif

@if (session('delete_success'))
    <div id="delete-success-toast" class="toast-success">
        {{ session('delete_success') }}
    </div>
@endif

@if (session('success'))
    <div id="success-toast" class="toast-success">
        {{ session('success') }}
    </div>
@endif

<div class="container">
    <div class="content">
    <div class="container-method">
        @if(isset($isAdmin) && $isAdmin)
            <div class="admin-header" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <h2 style="margin: 0; color: #333;">Методпакеты преподавателя: {{ $teacher->fio }}</h2>
                <a href="{{ route('admin.teacher.profile', $teacher->users_id) }}" class="action-btn" style="background: #2563eb; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">Назад к профилю</a>
            </div>
        @endif
        
        <div class="header">
        <h2>@if(isset($isAdmin) && $isAdmin)Методпакеты преподавателя{{ $teacher->fio }}@elseМетодпакеты@endif</h2>
        </div>
        <div class="filters">
            <form method="GET" action="{{ route('teacher.methodology') }}" class="filters-form">
                @if(isset($isAdmin) && $isAdmin)
                    <input type="hidden" name="teacher_id" value="{{ $teacher->users_id }}">
                @endif
                <select name="course" class="filter">
                    <option value="">Все курсы</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->name }}" {{ request('course') == $course->name ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
                
                @if(request('course'))
                    <a href="{{ route('teacher.methodology') }}@if(isset($isAdmin) && $isAdmin)?teacher_id={{ $teacher->users_id }}@endif" class="reset-filters">Сбросить фильтр</a>
                @endif
            </form>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Описание</th>
                        <th>Домашние задания</th>
                        <th>Уроки</th>
                        <th>Практические задания</th>
                        <th>Книги</th>
                        <th>Видео</th>
                        <th>Презентации</th>
                        <th>Тесты</th>
                        <th>Статьи</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    @foreach ($methods as $method)
                    <?php $i += 1; ?>
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $method->title }}</td>
                            <td>
                                @if($method->title_homework && is_array($method->title_homework) && $method->homework && is_array($method->homework))
                                    @for($i = 0; $i < min(count($method->title_homework), count($method->homework)); $i++)
                                        <a href="{{ \App\Helpers\FileHelper::getFileUrl($method->homework[$i]) }}" class="btn btn-primary" target="_blank">
                                            {{ $method->title_homework[$i] }}
                                        </a>
                                    @endfor
                                @else
                                    Нет данных
                                @endif
                            </td>
                            <td>
                                @if($method->title_lesson && is_array($method->title_lesson) && $method->lesson && is_array($method->lesson))
                                    @for($i = 0; $i < min(count($method->title_lesson), count($method->lesson)); $i++)
                                        <a href="{{ \App\Helpers\FileHelper::getFileUrl($method->lesson[$i]) }}" class="btn btn-primary" target="_blank">
                                            {{ $method->title_lesson[$i] }}
                                        </a>
                                    @endfor
                                @else
                                    Нет данных
                                @endif
                            </td>
                            <td>
                                @if($method->title_exercise && is_array($method->title_exercise) && $method->exercise && is_array($method->exercise))
                                    @for($i = 0; $i < min(count($method->title_exercise), count($method->exercise)); $i++)
                                        <a href="{{ \App\Helpers\FileHelper::getFileUrl($method->exercise[$i]) }}" class="btn btn-primary" target="_blank">
                                            {{ $method->title_exercise[$i] }}
                                        </a>
                                    @endfor
                                @else
                                    Нет данных
                                @endif
                            </td>
                            <td>
                                @if($method->title_book && is_array($method->title_book) && $method->book && is_array($method->book))
                                    @for($i = 0; $i < min(count($method->title_book), count($method->book)); $i++)
                                        <a href="{{ \App\Helpers\FileHelper::getFileUrl($method->book[$i]) }}" class="btn btn-primary" target="_blank">
                                            {{ $method->title_book[$i] }}
                                        </a>
                                    @endfor
                                @else
                                    Нет данных
                                @endif
                            </td>
                            <td>
                                @if($method->title_video && is_array($method->title_video) && $method->video && is_array($method->video))
                                    @for($i = 0; $i < min(count($method->title_video), count($method->video)); $i++)
                                        <a href="{{ \App\Helpers\FileHelper::getFileUrl($method->video[$i]) }}" class="btn btn-primary" target="_blank">
                                            {{ $method->title_video[$i] }}
                                        </a>
                                    @endfor
                                @else
                                    Нет данных
                                @endif
                            </td>
                            <td>
                                @if($method->title_presentation && is_array($method->title_presentation) && $method->presentation && is_array($method->presentation))
                                    @for($i = 0; $i < min(count($method->title_presentation), count($method->presentation)); $i++)
                                        <a href="{{ \App\Helpers\FileHelper::getFileUrl($method->presentation[$i]) }}" class="btn btn-primary" target="_blank">
                                            {{ $method->title_presentation[$i] }}
                                        </a>
                                    @endfor
                                @else
                                    Нет данных
                                @endif
                            </td>
                            <td>
                                @if($method->title_test && is_array($method->title_test) && $method->test && is_array($method->test))
                                    @for($i = 0; $i < min(count($method->title_test), count($method->test)); $i++)
                                        <a href="{{ \App\Helpers\FileHelper::getFileUrl($method->test[$i]) }}" class="btn btn-primary" target="_blank">
                                            {{ $method->title_test[$i] }}
                                        </a>
                                    @endfor
                                @else
                                    Нет данных
                                @endif
                            </td>
                            <td>
                                @if($method->title_article && is_array($method->title_article) && $method->article && is_array($method->article))
                                    @for($i = 0; $i < min(count($method->title_article), count($method->article)); $i++)
                                        <a href="{{ \App\Helpers\FileHelper::getFileUrl($method->article[$i]) }}" class="btn btn-primary" target="_blank">
                                            {{ $method->title_article[$i] }}
                                        </a>
                                    @endfor
                                @else
                                    Нет данных
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>
    </div>

    <style>
    .toast-success {
        position: fixed;
        top: 30px;
        right: 30px;
        z-index: 9999;
        background: #4caf50;
        color: #fff;
        padding: 16px 32px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        font-size: 16px;
        opacity: 0.95;
        animation: fadeIn 0.5s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 0.95; transform: translateY(0); }
    }
    </style>
    <script>
    // Автоматическое обновление при изменении курса
    document.addEventListener('DOMContentLoaded', function() {
        const courseSelect = document.querySelector('select[name="course"]');
        if (courseSelect) {
            courseSelect.addEventListener('change', function() {
                this.form.submit();
            });
        }
        
        var toast = document.getElementById('delete-success-toast');
        if (toast) {
            setTimeout(function() {
                toast.style.display = 'none';
            }, 3000);
        }
        
        var successToast = document.getElementById('success-toast');
        if (successToast) {
            setTimeout(function() {
                successToast.style.display = 'none';
            }, 3000);
        }
    });
    </script> 
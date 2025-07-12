@extends('admin.layouts.head')
@section('head')

<meta name="csrf-token" content="{{ csrf_token() }}">
@vite(['resources/css/statistic.css'])
@include('admin.layouts.adminNav')

<div class="container">
        
        <main class="content">
            <div class="statistics-container">
                <div class="statistics-header">
                    <h2>Статистика системы</h2>
                    <div class="period-selector">
                        <form method="GET" action="{{ route('statistic') }}" id="periodForm">
                            <select name="period" onchange="document.getElementById('periodForm').submit()">
                                <option value="all" {{ (isset($period) && $period == 'all') ? 'selected' : '' }}>За всё время</option>
                                <option value="week" {{ (isset($period) && $period == 'week') ? 'selected' : '' }}>За неделю</option>
                                <option value="month" {{ (isset($period) && $period == 'month') ? 'selected' : '' }}>За месяц</option>
                                <option value="year" {{ (isset($period) && $period == 'year') ? 'selected' : '' }}>За год</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="statistics-grid">
                    <div class="stat-card">
                        <h3>Общая успеваемость</h3>
                        <div class="chart-container">
                            <div class="chart-placeholder">
                                <div class="progress-circle">
                                    <div class="progress-fill" style="--progress: {{ $avg_grade * 20 }}%"></div>
                                    <span class="progress-text">{{ $avg_grade }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="stat-details">
                            <p><span>Средний балл:</span> <strong>{{ $avg_grade }}</strong></p>
                            <p><span>Отличников:</span> <strong>{{ $excellent_percent }}%</strong></p>
                            <p><span>Хорошистов:</span> <strong>{{ $good_percent }}%</strong></p>
                            <p><span>Троечников:</span> <strong>{{ $satisfactory_percent }}%</strong></p>
                            <p><span>Двоечников:</span> <strong>{{ $unsatisfactory_percent }}%</strong></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <h3>Посещаемость</h3>
                        <div class="chart-container">
                            <div class="chart-placeholder">
                                <div class="progress-circle">
                                    <div class="progress-fill" style="--progress: {{ $attendance }}%"></div>
                                    <span class="progress-text">{{ $attendance }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="stat-details">
                            <p><span>Средняя посещаемость:</span> <strong>{{ $attendance }}%</strong></p>
                            <p><span>Отличная:</span> <strong>{{ $excellent_attendance }}</strong></p>
                            <p><span>Хорошая:</span> <strong>{{ $good_attendance }}</strong></p>
                            <p><span>Удовлетворительная:</span> <strong>{{ $satisfactory_attendance }}</strong></p>
                            <p><span>Неудовлетворительная:</span> <strong>{{ $unsatisfactory_attendance }}</strong></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <h3>Активность преподавателей</h3>
                        <div class="chart-container">
                            <div class="chart-placeholder">
                                <div class="progress-circle">
                                    <div class="progress-fill" style="--progress: {{ $avg_teacher_rating * 20 }}%"></div>
                                    <span class="progress-text">{{ $avg_teacher_rating }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="stat-details">
                            <p><span>Средний рейтинг:</span> <strong>{{ $avg_teacher_rating }}/5</strong></p>
                            <p><span>Активных преподавателей:</span> <strong>{{ $teachers_count }}</strong></p>
                            <p><span>Среднее количество заданий:</span> <strong>{{ $avg_assignments }}</strong></p>
                            <p><span>Среднее время ответа:</span> <strong>{{ $avg_response_time }} часа</strong></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <h3>Выполнение домашних заданий</h3>
                        <div class="chart-container">
                            <div class="chart-placeholder">
                                <div class="progress-circle">
                                    <div class="progress-fill" style="--progress: {{ $completed_percent }}%"></div>
                                    <span class="progress-text">{{ $completed_percent }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="stat-details">
                            <p><span>Сдано вовремя:</span> <strong>{{ $completed_percent }}%</strong></p>
                            <p><span>Сдано с опозданием:</span> <strong>{{ $overdue_percent }}%</strong></p>
                            <p><span>Не сдано:</span> <strong>{{ $pending_percent }}%</strong></p>
                            <p><span>Средний балл:</span> <strong>{{ $avg_homework }}</strong></p>
                        </div>
                    </div>
                </div>
                <div class="detailed-statistics">
                    <h3>Подробная статистика по группам</h3>
                    <div class="groups-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Группа</th>
                                    <th>Средний балл</th>
                                    <th>Посещаемость</th>
                                    <th>Выполнение ДЗ</th>
                                    <th>Активность</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groups_stats as $group)
                                <tr>
                                    <td><strong>{{ $group['name'] }}</strong></td>
                                    <td>
                                        @if($group['avg_grade'] >= 4.5)
                                            <span class="badge badge-success">{{ $group['avg_grade'] }}</span>
                                        @elseif($group['avg_grade'] >= 4.0)
                                            <span class="badge badge-warning">{{ $group['avg_grade'] }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ $group['avg_grade'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($group['attendance'] >= 90)
                                            <span class="badge badge-success">{{ $group['attendance'] }}%</span>
                                        @elseif($group['attendance'] >= 80)
                                            <span class="badge badge-warning">{{ $group['attendance'] }}%</span>
                                        @else
                                            <span class="badge badge-danger">{{ $group['attendance'] }}%</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($group['homework_completion'] >= 4.5)
                                            <span class="badge badge-success">{{ $group['homework_completion'] }}</span>
                                        @elseif($group['homework_completion'] >= 4.0)
                                            <span class="badge badge-warning">{{ $group['homework_completion'] }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ $group['homework_completion'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($group['activity'] === 'Высокая')
                                            <span class="badge badge-success">{{ $group['activity'] }}</span>
                                        @elseif($group['activity'] === 'Средняя')
                                            <span class="badge badge-warning">{{ $group['activity'] }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ $group['activity'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="export-section">
                    <form method="GET" action="{{ route('statistic.export') }}" id="exportForm">
                        <input type="hidden" name="period" value="{{ $period ?? 'all' }}">
                        <select name="format" id="exportFormat">
                            <option value="xlsx">Excel</option>
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF</option>
                        </select>
                        <button type="submit" class="export-btn">
                            <span>Экспортировать статистику</span>
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
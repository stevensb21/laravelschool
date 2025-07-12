<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статистика по группам</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: center; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Статистика по группам</h2>
    <table>
        <thead>
            <tr>
                <th>Группа</th>
                <th>Средний балл</th>
                <th>Посещаемость (%)</th>
                <th>Выполнение ДЗ</th>
                <th>Активность</th>
            </tr>
        </thead>
        <tbody>
        @foreach($groups_stats as $group)
            <tr>
                <td>{{ $group['name'] }}</td>
                <td>{{ $group['avg_grade'] }}</td>
                <td>{{ $group['attendance'] }}</td>
                <td>{{ $group['homework_completion'] }}</td>
                <td>{{ $group['activity'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div style="text-align:right; font-size:10px; color:#888;">Сформировано: {{ date('d.m.Y H:i') }}</div>
</body>
</html> 
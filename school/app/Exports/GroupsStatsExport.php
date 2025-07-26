<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GroupsStatsExport implements FromArray, WithHeadings
{
    protected $groups_stats;

    public function __construct($groups_stats)
    {
        $this->groups_stats = $groups_stats;
    }

    public function array(): array
    {
        return collect($this->groups_stats)->map(function($group) {
            return [
                $group['name'] ?? '',
                ($group['avg_grade'] ?? '') === '' || $group['avg_grade'] == 0 ? '0' : (string)$group['avg_grade'],
                ($group['attendance'] ?? '') === '' || $group['attendance'] == 0 ? '0' : (string)$group['attendance'],
                ($group['homework_completion'] ?? '') === '' || $group['homework_completion'] == 0 ? '0' : (string)$group['homework_completion'],
                $group['activity'] ?? 'Низкая',
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return ['Группа', 'Средний балл', 'Посещаемость (%)', 'Выполнение ДЗ', 'Активность'];
    }
} 
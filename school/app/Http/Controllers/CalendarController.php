<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calendar;
use App\Models\Group;
use App\Models\Course;
use App\Models\Teacher;


class CalendarController extends Controller
{
    private const SUNDAY = 0;
    private const MONDAY = 1;
    

    public function index() {
        $user = auth()->user();
        $role = $user->role;
        $this->initializeWeekDates();

        if ($role === 'admin') {
            $data = $this->buildCompact();
            $data['isAdmin'] = true;
            $data['isTeacher'] = false;
            $data['isStudent'] = false;
            $data['edit_mode'] = request()->has('edit_mode');
            return view('admin.calendar', compact('data'));
        } elseif ($role === 'teacher') {
            $teacher = $user->teacher;
            // Получаем id курсов, которые ведёт преподаватель
            $courseIds = \App\Models\Course::where(function($q) use ($teacher) {
                $q->whereJsonContains('access_->teachers', (int)$teacher->users_id)
                  ->orWhereJsonContains('access_->teachers', (string)$teacher->users_id);
            })->pluck('id')->toArray();
            // Получаем только группы, которые проходят хотя бы один из этих курсов
            $groups = \App\Models\Group::where(function($q) use ($courseIds) {
                foreach ($courseIds as $cid) {
                    $q->orWhereJsonContains('courses', (int)$cid)
                      ->orWhereJsonContains('courses', (string)$cid);
                }
            })->get();
            // Получаем только названия предметов, которые ведёт преподаватель
            $subjects = \App\Models\Course::whereIn('id', $courseIds)->pluck('name')->toArray();
            // ВРЕМЕННО: dd для отладки
            dd([
                'courseIds' => $courseIds,
                'subjects' => $subjects,
                'groups' => $groups->pluck('name')->toArray(),
            ]);
            $query = \App\Models\Calendar::whereBetween('date_', [session('monday'), session('sunday')]);
            if (request('group')) {
                $query->where('name_group', request('group'));
            } else {
                $query->whereIn('name_group', $groups->pluck('name'));
            }
            if (request('subject')) {
                $query->where('subject', request('subject'));
            } else if (!empty($subjects)) {
                $query->whereIn('subject', $subjects);
            }
            $lessons = $query->get();
            $data = [
                'lessons' => $lessons,
                'groups' => $groups,
                'subjects' => $subjects,
                'teachers' => [$teacher],
                'user' => $user,
                'schedule' => $this->buildShedule($lessons),
                'selectedGroup' => request('group', ''),
                'selectedSubject' => request('subject', ''),
                'selectedTeacher' => $teacher->fio,
                'isAdmin' => false,
                'isTeacher' => true,
                'isStudent' => false,
            ];
            return view('teacher.calendar', compact('data'));
        } else { // student
            $student = $user->student;
            
            // Получаем все группы студента
            $studentGroups = $student->groups;
            $groupNames = $studentGroups->pluck('name')->toArray();
            
            // Получаем все предметы из всех групп студента
            $subjects = $student->getAllSubjects();
            
            // Получаем преподавателей для всех предметов студента
            $teachers = Teacher::whereJsonContains('subjects', $subjects)->get();
            
            // Получаем уроки для всех групп студента
            $query = \App\Models\Calendar::whereBetween('date_', [session('monday'), session('sunday')])
                ->whereIn('name_group', $groupNames);
                
            if (request('teacher')) {
                $query->where('teacher', request('teacher'));
            }
            if (request('subject')) {
                $query->where('subject', request('subject'));
            }
            if (request('group')) {
                $query->where('name_group', request('group'));
            }
            
            $lessons = $query->get();
            
            $data = [
                'lessons' => $lessons,
                'groups' => $studentGroups,
                'subjects' => $subjects,
                'teachers' => $teachers,
                'user' => $user,
                'schedule' => $this->buildShedule($lessons),
                'selectedGroup' => request('group', ''),
                'selectedSubject' => request('subject', ''),
                'selectedTeacher' => request('teacher', ''),
                'isAdmin' => false,
                'isTeacher' => false,
                'isStudent' => true,
            ];
            return view('student.calendar', compact('data'));
        }
    }

    private function buildCompact() {
        $this->initializeWeekDates();
        
        // Базовый запрос для уроков
        $query = Calendar::whereBetween('date_', [
            session('monday'),
            session('sunday')
        ]);

        // Применяем фильтры
        if (request('group')) {
            $query->where('name_group', request('group'));
        }
        if (request('teacher')) {
            $query->where('teacher', request('teacher'));
        }
        if (request('subject')) {
            $query->where('subject', request('subject'));
        }

        // Получаем отфильтрованные уроки
        $lessons = $query->get();
        
        $arr = [
            'lessons' => $lessons,
            'groups' => Group::getAll(),
            'subjects' => Course::getAll(),
            'teachers' => Teacher::getAll(),
            'user' => auth()->user(),
            'schedule' => $this->buildShedule($lessons),
            'selectedGroup' => request('group', ''),
            'selectedSubject' => request('subject', ''),
            'selectedTeacher' => request('teacher', '')
        ];

        return $arr;
    }

    private function buildShedule($lessons) {
        $schedule = array_fill(1, 7, array_fill(8, 22, null));
        foreach ($lessons as $lesson) {
            $lessonArr = is_array($lesson) ? $lesson : $lesson->toArray();
            $day = date('w', strtotime($lessonArr['date_']));
            if($day == 0) {
                $day = 7;
            }
            $start_timestamp = strtotime($lessonArr['start_time']);
            $end_timestamp = strtotime($lessonArr['end_time']);
            $start_hour = (int)date('G', $start_timestamp);
            $start_minute = (int)date('i', $start_timestamp);
            $end_hour = (int)date('G', $end_timestamp);
            $end_minute = (int)date('i', $end_timestamp);
            $lessonArr['partial_start'] = $start_minute > 0;
            $lessonArr['partial_end'] = $end_minute > 0;
            for ($hour = $start_hour; $hour <= $end_hour; $hour++) {
                if ($hour == $start_hour) {
                    $schedule[$day][$hour] = $lessonArr;
                } 
                elseif ($hour == $end_hour) {
                    if ($end_minute == 0 && $hour > $start_hour) {
                        continue;
                    }
                    $schedule[$day][$hour] = $lessonArr;
                } 
                else {
                    $schedule[$day][$hour] = $lessonArr;
                }
            }
        }
        return $schedule;
    }
    
    private function initializeWeekDates(): void
    {
        if (!session()->has(['monday', 'sunday'])) {
            $date = now();
            session()->put([
                'monday' => date('Y-m-d', strtotime('monday this week')),
                'sunday' => date('Y-m-d', strtotime('sunday this week'))
            ]);
        }
    }

    public function prevWeek() 
    {
        session()->put('monday', date('Y-m-d', strtotime(session('monday') . ' -7 days')));
        session()->put('sunday', date('Y-m-d', strtotime(session('sunday') . ' -7 days')));
        return redirect()->route('calendar');
    }

    public function nextWeek()
    {
        session()->put('monday', date('Y-m-d', strtotime(session('monday') . ' +7 days')));
        session()->put('sunday', date('Y-m-d', strtotime(session('sunday') . ' +7 days')));
        return redirect()->route('calendar');
    }

    public function addLesson(Request $request)
    {
        $user = auth()->user();
        $role = $user->role;
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'subject' => 'required|string',
            'name_group' => 'required|string',
            'end_time' => 'required|date_format:H:i',
            'teacher' => 'required|string'
        ]);

        // --- Проверка для преподавателя ---
        if ($role === 'teacher') {
            $teacher = $user->teacher;
            // Получаем только свои группы и предметы
            $groups = \App\Models\Group::whereJsonContains('courses', $teacher->subjects)->pluck('name')->toArray();
            $subjects = $teacher->subjects ?? [];
            if (!in_array($validated['name_group'], $groups) || !in_array($validated['subject'], $subjects) || $validated['teacher'] !== $teacher->fio) {
                return redirect()->back()->with('error', 'Вы можете добавлять уроки только для своих групп и предметов.');
            }
        }
        // --- Конец проверки ---

        $lessonData = [
            'date_' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'subject' => $validated['subject'],
            'name_group' => $validated['name_group'],
            'teacher' => $validated['teacher']
        ];

        $lesson = new Calendar();
        $lesson->createItem($lessonData);

        if (request()->has('edit_mode')) {
            return redirect()->route('calendar', ['edit_mode' => 1]);
        }
        return redirect('calendar');
    }

    public function editMode(Request $request)
    {
        $data = $this->buildCompact();
        $data['isAdmin'] = true;
        $data['isTeacher'] = false;
        $data['isStudent'] = false;
        $data['edit_mode'] = true;
        return view('admin/calendar', compact('data'));
    }


    public function deleteLesson(Request $request)
    {
        $lessonId = $request->input('lesson_id');
        $lesson = Calendar::find($lessonId);
        
        if ($lesson) {
            $lesson->delete();
        }
        
        // Возвращаемся в режим редактирования, если пользователь был в нем
        if (request()->has('edit_mode')) {
            return redirect()->route('calendar', ['edit_mode' => 1]);
        }
        
        return redirect('calendar');
    }

    public function AddRow() {
        $post = new Calendar();
        $postArr = [
            'date_' => '2025-01-23',
            'subject' => 'C++',
            'name_group' => '10320-C22',
            'start_time' => '11:00:00',
            'end_time'=> '13:00:00',
            'teacher' => 'Никита',
        ];
        $post->createItem($postArr);
        echo 'created';
    }
}

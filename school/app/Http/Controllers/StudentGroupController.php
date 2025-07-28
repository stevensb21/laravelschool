<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class StudentGroupController extends Controller
{
    /**
     * Показать группы студента
     */
    public function index($studentId)
    {
        $student = Student::with(['groups', 'primaryGroup'])->findOrFail($studentId);
        $allGroups = Group::all();
        
        return view('admin.student-groups', compact('student', 'allGroups'));
    }
    
    /**
     * Добавить студента в группу
     */
    public function addToGroup(Request $request, $studentId)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'is_primary' => 'boolean'
        ]);
        
        $student = Student::findOrFail($studentId);
        $groupId = $request->group_id;
        $isPrimary = $request->boolean('is_primary');
        
        // Проверяем, не состоит ли уже студент в этой группе
        if ($student->groups()->where('group_id', $groupId)->exists()) {
            return redirect()->back()->with('error', 'Студент уже состоит в этой группе');
        }
        
        try {
            $student->addToGroup($groupId, $isPrimary);
            
            // Обновляем предметы студента
            $allSubjects = $student->getAllSubjects();
            $student->update(['subjects' => $allSubjects]);
            
            // Добавляем студента в чат группы
            $this->addStudentToGroupChat($student, $groupId);
            
            return redirect()->back()->with('success', 'Студент успешно добавлен в группу');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка при добавлении студента в группу: ' . $e->getMessage());
        }
    }
    
    /**
     * Удалить студента из группы
     */
    public function removeFromGroup(Request $request, $studentId)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id'
        ]);
        
        $student = Student::findOrFail($studentId);
        $groupId = $request->group_id;
        
        try {
            $student->removeFromGroup($groupId);
            
            // Обновляем предметы студента
            $allSubjects = $student->getAllSubjects();
            $student->update(['subjects' => $allSubjects]);
            
            // Удаляем студента из чата группы
            $this->removeStudentFromGroupChat($student, $groupId);
            
            return redirect()->back()->with('success', 'Студент успешно удален из группы');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка при удалении студента из группы: ' . $e->getMessage());
        }
    }
    
    /**
     * Установить основную группу
     */
    public function setPrimaryGroup(Request $request, $studentId)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id'
        ]);
        
        $student = Student::findOrFail($studentId);
        $groupId = $request->group_id;
        
        // Проверяем, состоит ли студент в этой группе
        if (!$student->groups()->where('group_id', $groupId)->exists()) {
            return redirect()->back()->with('error', 'Студент не состоит в этой группе');
        }
        
        try {
            $student->setPrimaryGroup($groupId);
            return redirect()->back()->with('success', 'Основная группа успешно изменена');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка при изменении основной группы: ' . $e->getMessage());
        }
    }
    
    /**
     * Показать список групп для выбора
     */
    public function groupsList()
    {
        $groups = Group::withCount(['allStudents', 'primaryStudents'])->get();
        
        return view('admin.groups-list', compact('groups'));
    }
    
    /**
     * Показать студентов группы
     */
    public function groupStudents($groupId)
    {
        $group = Group::with(['allStudents', 'primaryStudents'])->findOrFail($groupId);
        
        return view('admin.group-students', compact('group'));
    }
    
    /**
     * Добавить студента в группу (для администратора)
     */
    public function addStudentToGroup(Request $request, $groupId)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'is_primary' => 'boolean'
        ]);
        
        $group = Group::findOrFail($groupId);
        $studentId = $request->student_id;
        $isPrimary = $request->boolean('is_primary');
        
        // Проверяем, не состоит ли уже студент в этой группе
        if ($group->allStudents()->where('student_id', $studentId)->exists()) {
            return redirect()->back()->with('error', 'Студент уже состоит в этой группе');
        }
        
        try {
            $group->addStudent($studentId, $isPrimary);
            
            // Обновляем предметы студента
            $student = Student::find($studentId);
            $allSubjects = $student->getAllSubjects();
            $student->update(['subjects' => $allSubjects]);
            
            // Добавляем студента в чат группы
            $this->addStudentToGroupChat($student, $groupId);
            
            return redirect()->back()->with('success', 'Студент успешно добавлен в группу');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка при добавлении студента в группу: ' . $e->getMessage());
        }
    }
    
    /**
     * Удалить студента из группы (для администратора)
     */
    public function removeStudentFromGroup(Request $request, $groupId)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);
        
        $group = Group::findOrFail($groupId);
        $studentId = $request->student_id;
        
        try {
            $group->removeStudent($studentId);
            
            // Обновляем предметы студента
            $student = Student::find($studentId);
            $allSubjects = $student->getAllSubjects();
            $student->update(['subjects' => $allSubjects]);
            
            // Удаляем студента из чата группы
            $this->removeStudentFromGroupChat($student, $groupId);
            
            return redirect()->back()->with('success', 'Студент успешно удален из группы');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка при удалении студента из группы: ' . $e->getMessage());
        }
    }
    
    /**
     * Добавить студента в чат группы
     */
    private function addStudentToGroupChat($student, $groupId)
    {
        try {
            // Находим чат группы
            $groupChat = \App\Models\GroupChat::where('group_id', $groupId)->first();
            
            // Если чат не существует, создаем его
            if (!$groupChat) {
                $group = \App\Models\Group::find($groupId);
                if ($group) {
                    $groupChat = \App\Models\GroupChat::create([
                        'group_id' => $groupId,
                        'name' => 'Чат группы ' . $group->name
                    ]);
                    
                    // Добавляем преподавателя группы в чат
                    if ($group->teacher_id) {
                        $teacher = \App\Models\Teacher::find($group->teacher_id);
                        if ($teacher && $teacher->users_id) {
                            \App\Models\UserChat::create([
                                'group_chat_id' => $groupChat->id,
                                'user_id' => $teacher->users_id
                            ]);
                        }
                    }
                }
            }
            
            if ($groupChat) {
                // Проверяем, не добавлен ли уже студент в чат
                $exists = \App\Models\UserChat::where('group_chat_id', $groupChat->id)
                    ->where('user_id', $student->users_id)
                    ->exists();
                
                if (!$exists) {
                    \App\Models\UserChat::create([
                        'group_chat_id' => $groupChat->id,
                        'user_id' => $student->users_id
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Ошибка при добавлении студента в чат группы', [
                'student_id' => $student->id,
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Удалить студента из чата группы
     */
    private function removeStudentFromGroupChat($student, $groupId)
    {
        try {
            // Находим чат группы
            $groupChat = \App\Models\GroupChat::where('group_id', $groupId)->first();
            
            if ($groupChat) {
                // Удаляем студента из чата
                \App\Models\UserChat::where('group_chat_id', $groupChat->id)
                    ->where('user_id', $student->users_id)
                    ->delete();
            }
        } catch (\Exception $e) {
            \Log::error('Ошибка при удалении студента из чата группы', [
                'student_id' => $student->id,
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);
        }
    }
} 
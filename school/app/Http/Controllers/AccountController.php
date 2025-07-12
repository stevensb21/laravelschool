<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Group;
use App\Models\Appeal;

class AccountController extends Controller
{
    public function index() {
        $user = auth()->user();
        session()->put("user", $user);
        if ($user->role === 'admin') {
            $totalStudents = Student::count();
            $totalTeachers = Teacher::count();
            $activeGroups = Group::count();
            $newAppeals = Appeal::where('status', 'Активно')->count();
            return view('admin/account', compact('user', 'totalStudents', 'totalTeachers', 'activeGroups', 'newAppeals'));
        } elseif ($user->role === 'teacher') {
            $teacher = Teacher::where('users_id', $user->id)->first();
            return view('teacher/account', compact('teacher'));
        } else {
            $student = $user->student;
            $group = $student->group;
            $subjects = $group->subjects;
            return view('student/account', compact('user', 'student', 'subjects'));
        }
    }
}

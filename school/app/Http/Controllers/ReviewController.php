<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReviewController extends Controller
{
    // Показывать отзывы для преподавателя или студента
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $request->has('isAdmin') && $request->isAdmin;
        
        // Определяем тип пользователя и получаем соответствующие данные
        if ($user->role === 'teacher') {
            $teacher = Teacher::where('users_id', $user->id)->first();
            $students = Student::all(); // Все студенты для выбора
            
            // Получаем отзывы, где текущий пользователь является отправителем или получателем
            $reviews = Review::where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('recipient_id', $user->id);
            })
            ->with(['sender', 'recipient'])
            ->latest()
            ->get();
            
            // Добавляем имена отправителей и получателей
            $reviews->each(function($review) {
                if ($review->sender_type === 'teacher') {
                    $sender = Teacher::where('users_id', $review->sender_id)->first();
                    $review->sender_name = $sender ? $sender->fio : 'Преподаватель';
                } else {
                    $sender = Student::where('users_id', $review->sender_id)->first();
                    $review->sender_name = $sender ? $sender->fio : 'Студент';
                }
                
                if ($review->recipient_type === 'teacher') {
                    $recipient = Teacher::find($review->recipient_id);
                    $review->recipient_name = $recipient ? $recipient->fio : 'Преподаватель';
                } else {
                    $recipient = Student::find($review->recipient_id);
                    $review->recipient_name = $recipient ? $recipient->fio : 'Студент';
                }
            });
            
            return view('teacher.reviews', compact('reviews', 'students', 'teacher', 'isAdmin'));
            
        } elseif ($user->role === 'student') {
            $student = Student::where('users_id', $user->id)->first();
            $teachers = Teacher::all(); // Все преподаватели для выбора
            
            // Получаем отзывы, где текущий пользователь является отправителем или получателем
            $reviews = Review::where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('recipient_id', $user->id);
            })
            ->with(['sender', 'recipient'])
            ->latest()
            ->get();
            
            // Добавляем имена отправителей и получателей
            $reviews->each(function($review) {
                if ($review->sender_type === 'teacher') {
                    $sender = Teacher::where('users_id', $review->sender_id)->first();
                    $review->sender_name = $sender ? $sender->fio : 'Преподаватель';
                } else {
                    $sender = Student::where('users_id', $review->sender_id)->first();
                    $review->sender_name = $sender ? $sender->fio : 'Студент';
                }
                
                if ($review->recipient_type === 'teacher') {
                    $recipient = Teacher::find($review->recipient_id);
                    $review->recipient_name = $recipient ? $recipient->fio : 'Преподаватель';
                } else {
                    $recipient = Student::find($review->recipient_id);
                    $review->recipient_name = $recipient ? $recipient->fio : 'Студент';
                }
            });
            
            return view('student.reviews', compact('reviews', 'teachers', 'student', 'isAdmin'));
        }
        
        return redirect()->back()->with('error', 'Неизвестная роль пользователя.');
    }

    // Отправка отзыва
    public function store(Request $request)
    {
        \Log::info('Попытка отправки отзыва', $request->all());
        $request->validate([
            'recipient_id' => 'required|integer',
            'review_text' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $user = Auth::user();
        $recipientType = $user->role === 'teacher' ? 'student' : 'teacher';
        \Log::info('Определён recipientType', ['recipientType' => $recipientType, 'user_role' => $user->role]);

        // Проверяем, что получатель существует
        if ($recipientType === 'teacher') {
            $recipient = Teacher::find($request->recipient_id);
        } else {
            $recipient = Student::find($request->recipient_id);
        }

        if (!$recipient) {
            \Log::warning('Получатель не найден', ['recipient_id' => $request->recipient_id, 'recipientType' => $recipientType]);
            return back()->with('error', 'Получатель не найден.');
        }

        $review = Review::create([
            'sender_id' => $user->id,
            'sender_type' => $user->role,
            'recipient_id' => $request->recipient_id,
            'recipient_type' => $recipientType,
            'review_text' => $request->review_text,
            'rating' => $request->rating,
            'status' => 'pending',
        ]);
        \Log::info('Отзыв создан', ['review_id' => $review->id]);

        return back()->with('success', 'Ваш отзыв отправлен и появится после проверки администратором.');
    }

    // Для администратора: список отзывов на модерацию
    public function adminIndex()
    {
        $reviews = Review::with(['sender', 'recipient', 'moderator'])
            ->latest()
            ->get();

        // Добавляем имена отправителей и получателей
        $reviews->each(function($review) {
            if ($review->sender_type === 'teacher') {
                $sender = Teacher::where('users_id', $review->sender_id)->first();
                $review->sender_name = $sender ? $sender->fio : 'Преподаватель';
            } else {
                $sender = Student::where('users_id', $review->sender_id)->first();
                $review->sender_name = $sender ? $sender->fio : 'Студент';
            }
            
            if ($review->recipient_type === 'teacher') {
                $recipient = Teacher::find($review->recipient_id);
                $review->recipient_name = $recipient ? $recipient->fio : 'Преподаватель';
            } else {
                $recipient = Student::find($review->recipient_id);
                $review->recipient_name = $recipient ? $recipient->fio : 'Студент';
            }
        });
        //dd($reviews->toArray());
        return view('admin.reviews.index', compact('reviews'));
    }

    // Подтвердить отзыв
    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $review->status = 'approved';
        $review->moderated_by = Auth::id();
        $review->moderated_at = Carbon::now();
        $review->save();

        return back()->with('success', 'Отзыв подтверждён.');
    }

    // Отклонить отзыв
    public function reject(Request $request, $id)
    {
        $request->validate([
            'moderation_comment' => 'required|string|min:5',
        ]);

        $review = Review::findOrFail($id);
        $review->status = 'rejected';
        $review->moderated_by = Auth::id();
        $review->moderated_at = Carbon::now();
        $review->moderation_comment = $request->moderation_comment;
        $review->save();

        return back()->with('success', 'Отзыв отклонён.');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();
        return back()->with('success', 'Отзыв удалён.');
    }
}

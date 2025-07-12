<?php

namespace App\Http\Controllers;

use App\Models\Appeal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppealController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Appeal::with(['sender.student', 'sender.teacher', 'recipient.student', 'recipient.teacher']);

        // Фильтр по типу обращения
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Фильтр по поиску
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('sender', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('recipient', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Показываем обращения в зависимости от роли пользователя
        if ($user->role === 'admin') {
            // Админ видит все обращения
            $appeals = $query->orderBy('created_at', 'desc')->get();
        } elseif ($user->role === 'teacher') {
            // Преподаватель видит обращения, где он отправитель или получатель
            $query->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
            });
            $appeals = $query->orderBy('created_at', 'desc')->get();
        } else {
            // Студент видит только свои обращения
            $query->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
            });
            $appeals = $query->orderBy('created_at', 'desc')->get();
        }

        // Получаем пользователей с их ФИО из связанных таблиц
        $users = User::with(['student', 'teacher'])->get();
        
        return view('admin.appeals', compact('appeals', 'users'));
    }

    public function show($id)
    {
        $appeal = Appeal::with(['sender.student', 'sender.teacher', 'recipient.student', 'recipient.teacher'])->findOrFail($id);
        
        $html = view('admin.appeal-details', compact('appeal'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'recipient_id' => 'required|exists:users,id',
            'type' => 'required|string|in:Вопрос,Жалоба,Предложение,Другое',
            'description' => 'required|string|max:1000'
        ]);

        $appeal = Appeal::create([
            'title' => $request->title,
            'sender_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'type' => $request->type,
            'description' => $request->description,
            'status' => 'Активно'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Обращение успешно создано',
            'appeal' => $appeal->load(['sender', 'recipient'])
        ]);
    }

    public function update(Request $request, $id)
    {
        $appeal = Appeal::findOrFail($id);
        $user = Auth::user();

        // Ответ на обращение (feedback)
        if ($request->has('feedback') && !$request->has('like_feedback')) {
            $request->validate([
                'feedback' => 'required|string|max:1000',
            ]);
            if ($appeal->recipient_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав для ответа на это обращение'
                ], 403);
            }
            $appeal->update([
                'feedback' => $request->feedback,
                'status' => 'Завершено'
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Ответ успешно добавлен',
                'appeal' => $appeal->load(['sender', 'recipient'])
            ]);
        }

        // Оценка ответа (like_feedback)
        if ($request->has('like_feedback') && !$request->has('feedback')) {
            $request->validate([
                'like_feedback' => 'required|integer|between:1,5'
            ]);
            if ($appeal->sender_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Оценивать может только отправитель обращения'
                ], 403);
            }
            if (!$appeal->feedback) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя оценить, пока не получен ответ на обращение'
                ], 403);
            }
            $appeal->update([
                'like_feedback' => $request->like_feedback
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Оценка успешно добавлена',
                'appeal' => $appeal->load(['sender', 'recipient'])
            ]);
        }

        // Если оба поля или ни одно — ошибка
        return response()->json([
            'success' => false,
            'message' => 'Некорректный запрос'
        ], 400);
    }

    public function destroy($id)
    {
        $appeal = Appeal::findOrFail($id);
        
        // Проверяем права на удаление
        if ($appeal->sender_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для удаления этого обращения'
            ], 403);
        }

        $appeal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Обращение успешно удалено'
        ]);
    }
}
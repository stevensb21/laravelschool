<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupChat;
use App\Models\UserChat;
use App\Models\ChatMessage;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Список чатов пользователя
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $chat = GroupChat::where('name', 'Чат с преподавателями')->first();
            $userChats = collect();
            if ($chat) {
                $userChat = UserChat::where('user_id', $user->id)->where('group_chat_id', $chat->id)->with('groupChat')->first();
                if ($userChat) {
                    $userChats = collect([$userChat]);
                }
            }
        } else {
            $userChats = UserChat::where('user_id', $user->id)->with('groupChat')->get();
        }
        return view('chats.index', [
            'userChats' => $userChats
        ]);
    }

    // Просмотр сообщений в чате
    public function show($chatId)
    {
        $userId = Auth::id();
        $userChat = UserChat::where('user_id', $userId)->where('group_chat_id', $chatId)->firstOrFail();
        $chat = GroupChat::findOrFail($chatId);
        $messages = ChatMessage::where('group_chat_id', $chatId)->with('user')->orderBy('created_at')->get();
        $isTeacher = false;
        // Проверяем, является ли пользователь преподавателем этой группы
        $teacher = Teacher::where('users_id', $userId)->first();
        if ($teacher && $chat->group && $chat->group->teacher_id == $teacher->id) {
            $isTeacher = true;
        }
        // Получаем участников чата с ФИО и ролью
        $participants = UserChat::where('group_chat_id', $chatId)->with('user')->get()->map(function($uc) {
            $user = $uc->user;
            if (!$user) return null;
            if ($user->role === 'teacher') {
                $teacher = \App\Models\Teacher::where('users_id', $user->id)->first();
                return [
                    'fio' => $teacher ? $teacher->fio : $user->name,
                    'role' => 'teacher',
                ];
            } elseif ($user->role === 'student') {
                $student = \App\Models\Student::where('users_id', $user->id)->first();
                return [
                    'fio' => $student ? $student->fio : $user->name,
                    'role' => 'student',
                ];
            } else {
                return [
                    'fio' => $user->name,
                    'role' => 'admin',
                ];
            }
        })->filter()->values();
        return view('chats.show', [
            'chat' => $chat,
            'messages' => $messages,
            'isTeacher' => $isTeacher,
            'participants' => $participants,
        ]);
    }

    // Отправка сообщения
    public function sendMessage(Request $request, $chatId)
    {
        $userId = Auth::id();
        \Log::info('Отправка сообщения', [
            'user_id' => $userId,
            'chat_id' => $chatId,
            'message' => $request->message,
            'has_file' => $request->hasFile('file'),
            'user_in_userchats' => \App\Models\UserChat::where('user_id', $userId)->where('group_chat_id', $chatId)->exists(),
        ]);
        $request->validate([
            'message' => 'nullable|string|max:2000',
            'file' => 'nullable|file|max:10240', // до 10 МБ
        ]);
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $targetName = time() . '_' . $file->getClientOriginalName();
            $targetPath = storage_path('app/public/chat_files/' . $targetName);
            $result = copy($file->getPathname(), $targetPath);
            \Log::info('Результат ручного копирования', [
                'from' => $file->getPathname(),
                'to' => $targetPath,
                'result' => $result,
                'file_exists' => file_exists($targetPath),
            ]);
            $filePath = 'storage/chat_files/' . $targetName;
        }
        if (empty($request->message) && !$filePath) {
            \Log::info('Ошибка: пустое сообщение и нет файла');
            return back()->withErrors(['message' => 'Введите сообщение или прикрепите файл.']);
        }
        $msg = ChatMessage::create([
            'group_chat_id' => $chatId,
            'user_id' => $userId,
            'message' => $request->message ?? '',
            'file_path' => $filePath,
        ]);
        \Log::info('Сообщение создано', ['msg_id' => $msg->id]);
        return redirect()->route('chats.show', $chatId);
    }

    // Удаление истории чата (только для преподавателя)
    public function clearHistory($chatId)
    {
        $userId = Auth::id();
        $chat = GroupChat::findOrFail($chatId);
        $teacher = Teacher::where('users_id', $userId)->first();
        if (!$teacher || !$chat->group || $chat->group->teacher_id != $teacher->id) {
            abort(403, 'Только преподаватель может очищать историю чата');
        }
        ChatMessage::where('group_chat_id', $chatId)->delete();
        return redirect()->route('chats.show', $chatId)->with('success', 'История чата удалена');
    }

    // AJAX: Получить новые сообщения
    public function fetchMessages(Request $request, $chatId)
    {
        $lastId = $request->input('after_id', 0);
        $messages = ChatMessage::where('group_chat_id', $chatId)
            ->where('id', '>', $lastId)
            ->with('user')
            ->orderBy('id')
            ->get();
        $html = '';
        foreach ($messages as $msg) {
            $html .= view('chats._message', ['msg' => $msg])->render();
        }
        return response()->json([
            'html' => $html,
            'last_id' => $messages->last()?->id ?? $lastId,
        ]);
    }

    // Создать чат с преподавателями (только для администратора)
    public function createTeachersChat(Request $request)
    {
        $user = Auth::user();
        \Log::info('Создание чата с преподавателями', [
            'admin_id' => $user->id,
            'admin_role' => $user->role,
            'teachers_count' => \App\Models\User::where('role', 'teacher')->count()
        ]);
        if ($user->role !== 'admin') {
            abort(403);
        }
        $chat = GroupChat::where('name', 'Чат с преподавателями')->first();
        if (!$chat) {
            $chat = GroupChat::create(['name' => 'Чат с преподавателями', 'group_id' => null]);
        }
        // Добавить админа
        UserChat::firstOrCreate(['group_chat_id' => $chat->id, 'user_id' => $user->id]);
        // Добавить всех админов
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            if ($admin && $admin->id) {
                UserChat::firstOrCreate(['group_chat_id' => $chat->id, 'user_id' => $admin->id]);
            }
        }
        // Добавить всех преподавателей
        $teachers = \App\Models\User::where('role', 'teacher')->get();
        foreach ($teachers as $t) {
            if ($t && $t->id) {
                UserChat::firstOrCreate(['group_chat_id' => $chat->id, 'user_id' => $t->id]);
            }
        }
        return redirect()->route('chats.index')->with('success', 'Чат с преподавателями создан!');
    }
} 
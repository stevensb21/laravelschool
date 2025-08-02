@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/students.css'])

@php $user = auth()->user(); @endphp
@if($user->role === 'admin')
@include('admin.layouts.adminNav')
@elseif($user->role === 'teacher')
    @include('teacher.nav')
@elseif($user->role === 'student')
    @include('student.nav')
@endif

<div class="container">
    <main class="content">
        <div class="index" style="background:var(--card-bg);border-radius:12px;box-shadow:0 2px 8px var(--card-shadow);padding:24px;margin:0 auto;">
            <h2 style=" margin-bottom: 30px;">Чаты групп</h2>
            @if($user->role === 'admin')
                @php $hasTeachersChat = \App\Models\GroupChat::where('name', 'Чат с преподавателями')->exists(); @endphp
                @if(!$hasTeachersChat)
                    <form method="POST" action="{{ route('chats.createTeachersChat') }}" style="text-align:center; margin-bottom: 24px;">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="padding: 8px 22px; border-radius: 7px; background: var(--btn-primary); color: var(--text-light); font-weight: 500;">Создать чат с преподавателями</button>
                    </form>
                @endif
            @endif
            @if($userChats->isEmpty())
                <div style="text-align:center; color: var(--text-muted);">У вас пока нет чатов</div>
            @else
                <div class="chat-list" style="display: flex; flex-direction: column; gap: 18px;">
                    @foreach($userChats as $userChat)
                        <div class="chat-card" style="background: var(--card-bg); border-radius: 10px; box-shadow: 0 2px 8px var(--card-shadow); padding: 18px 22px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <strong>{{ $userChat->groupChat->name ?? 'Групповой чат' }}</strong>
                                @if($userChat->groupChat && $userChat->groupChat->group)
                                    <span style="color: var(--text-muted); font-size: 14px; margin-left: 10px;">({{ $userChat->groupChat->group->name }})</span>
                                @endif
                            </div>
                            <a href="{{ route('chats.show', $userChat->group_chat_id) }}" class="btn btn-primary" style="padding: 7px 18px; border-radius: 6px; background: var(--btn-primary); color: var(--text-light); text-decoration: none; font-weight: 500;">Открыть</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
</div>

<style>
/* Адаптивность для списка чатов */
@media (max-width: 768px) {
    .index {
        padding: 20px !important;
    }
}
</style>
@endsection



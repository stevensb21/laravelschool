@extends('admin.layouts.head')
@section('head')
@vite(['resources/css/colors.css'])

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
    <div class="chat-box" style="background: #fff; border-radius: 15px; padding: 20px; box-shadow: 0 4px 6px #0001; border: 1px solid #eee; height: calc(100vh - 100px); display: flex; flex-direction: column;">
        <h2 style="text-align:center; margin-bottom: 15px; flex-shrink: 0;">{{ $chat->name }}</h2>
        <div style="margin-bottom: 10px; text-align:center; flex-shrink: 0;">
            <button id="showParticipantsBtn" class="participants-btn" style="background:var(--btn-primary);color:var(--text-light);padding:7px 18px;border:none;border-radius:6px;font-weight:500;cursor:pointer;">
                Участники чата ({{ $participants->count() }})
            </button>
        </div>
        <!-- Модальное окно участников -->
        <div id="participantsModal" class="participants-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:var(--modal-overlay);z-index:10000;align-items:center;justify-content:center;">
            <div class="participants-list" style="background:var(--modal-bg);border-radius:12px;max-width:400px;width:90vw;max-height:70vh;overflow-y:auto;padding:24px 18px;box-shadow:0 4px 24px var(--card-shadow);">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <strong style="font-size:18px;">Участники чата</strong>
                    <button onclick="document.getElementById('participantsModal').style.display='none'" style="background:none;border:none;font-size:22px;cursor:pointer;color:var(--error-color);">×</button>
                </div>
                @foreach($participants as $p)
                    <div class="participant-row" style="display:flex;align-items:center;margin-bottom:12px;padding:6px 0;border-bottom:1px solid var(--border-light);">
                        <img src="{{ asset('images/man.jpg') }}" alt="avatar" style="width:32px;height:32px;border-radius:50%;margin-right:12px;">
                        <div style="flex:1;">
                            <span style="font-weight:500;">{{ $p['fio'] }}</span>
                            <span class="role-badge {{ $p['role'] }}" style="margin-left:8px;padding:2px 8px;border-radius:5px;font-size:12px;font-weight:500;background:var(--{{ $p['role'] == 'admin' ? 'accent-dark' : ($p['role'] == 'teacher' ? 'success-color' : 'info-color') }});color:var(--text-light);">{{ $p['role'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="chat-messages" id="chatMessages" style="background: #f9f9f9; border-radius: 10px; padding: 15px; flex: 1; overflow-y: auto; margin-bottom: 15px; min-height: 0;">
            @php $lastId = 0; @endphp
            @forelse($messages as $msg)
                @php $lastId = $msg->id; @endphp
                @include('chats._message', ['msg' => $msg])
            @empty
                <div style="text-align:center; color:#aaa;">Нет сообщений</div>
            @endforelse
        </div>
        <form id="chatSendForm" method="POST" action="{{ route('chats.send', $chat->id) }}" style="display:flex; gap:10px; align-items:center; justify-content:center; flex-shrink: 0;" enctype="multipart/form-data">
            @csrf
            <input type="text" name="message" id="msgInput" placeholder="Введите сообщение..." style="flex:1; padding:8px 12px; border-radius:6px; border:1px solid #ccc; height:40px; font-size:16px;">
            <label for="fileInput" class="file-attach-btn" title="Прикрепить файл" style="display:flex !important;align-items:center !important;justify-content:center !important;color:var(--text-light);border:none;border-radius:6px;width:40px;height:40px;cursor:pointer;transition:background 0.18s;">
                <svg width="22" height="22" fill="none" stroke="#333" stroke-width="2" viewBox="0 0 24 24" style="display:block !important;margin:0 !important;position:relative !important;top:0 !important;left:0 !important;transform:none !important;"><path d="M21 12.3V17a5 5 0 0 1-10 0V7a3 3 0 0 1 6 0v9a1 1 0 0 1-2 0V8" /></svg>
            </label>
            <input type="file" name="file" id="fileInput" style="display:none;">
            <button type="submit" class="btn btn-primary" style="padding:8px 18px; border-radius:6px; background:var(--btn-primary); color:var(--text-light); font-weight:500; transition:background 0.2s; height:40px;">Отправить</button>
        </form>
        <div id="newMsgNotify" style="display:none;position:absolute;left:50%;transform:translateX(-50%);top:10px;background:#2563eb;color:#fff;padding:7px 18px;border-radius:6px;z-index:10;font-weight:500;box-shadow:0 2px 8px #0002;">Новое сообщение</div>
        <script>
        const showBtn = document.getElementById('showParticipantsBtn');
        const modal = document.getElementById('participantsModal');
        if (showBtn && modal) {
            showBtn.onclick = function() {
                modal.style.display = 'flex';
            };
            // Закрытие по клику вне окна
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
        let lastMsgId = {{ $lastId }};
        let chatId = {{ $chat->id }};
        let polling = true;
        let chatBox = document.getElementById('chatMessages');
        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        function showNotify() {
            let n = document.getElementById('newMsgNotify');
            n.style.display = 'block';
            setTimeout(()=>{ n.style.display = 'none'; }, 1500);
        }
        function fetchNewMessages() {
            if (!polling) return;
            fetch(`/chats/${chatId}/fetch?after_id=${lastMsgId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.html && data.html.length > 0) {
                        chatBox.insertAdjacentHTML('beforeend', data.html);
                        lastMsgId = data.last_id;
                        showNotify();
                        scrollToBottom();
                    }
                })
                .catch(()=>{});
        }
        setInterval(fetchNewMessages, 3000);
        // Автопрокрутка при загрузке
        scrollToBottom();

        // --- Асинхронная отправка формы ---
        document.getElementById('chatSendForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let form = e.target;
            let formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(r => r.redirected ? window.location.href = r.url : r.json().catch(()=>null))
            .then(data => {
                // После отправки — сбросить поля
                form.reset();
                document.getElementById('msgInput').value = '';
                document.getElementById('fileInput').value = '';
                setTimeout(fetchNewMessages, 300); // Быстрее получить новое сообщение
                scrollToBottom();
            })
            .catch(()=>{});
        });
        </script>
        @if($isTeacher)
            <form method="POST" action="{{ route('chats.clear', $chat->id) }}" style="margin-top:10px; text-align:right; flex-shrink: 0;">
                @csrf
                <button type="submit" class="btn btn-danger" style="background:var(--error-color); color:#fff; border-radius:6px; padding:7px 16px; font-weight:500;">Очистить историю чата</button>
            </form>
        @endif
        <div style="margin-top:10px; text-align:center; flex-shrink: 0;">
            <a href="{{ route('chats.index') }}" style="color:var(--link-color); text-decoration:underline; transition:color 0.2s;" onmouseover="this.style.color='var(--link-hover)'" onmouseout="this.style.color='var(--link-color)'">← К списку чатов</a>
        </div>
    </div>
</main>
</div>

<style>
/* Адаптивность для чата */
.container {
    height: 100vh;
    overflow-y: auto;
}

.content {
    height: 100%;
    padding: 20px;
    box-sizing: border-box;
}

.chat-box {
    height: calc(100vh - 40px) !important;
    display: flex !important;
    flex-direction: column !important;
}

.chat-messages {
    flex: 1 !important;
    min-height: 0 !important;
    overflow-y: auto !important;
}

@media (max-width: 768px) {
    .content {
        padding: 10px;
    }
    
    .chat-box {
        height: calc(100vh - 100px) !important;
        padding: 15px !important;
    }
    
    .chat-messages {
        padding: 10px !important;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .chat-box {
        height: calc(100vh - 40px) !important;
    }
}

@media (min-width: 1025px) {
    .chat-box {
        height: calc(100vh - 40px) !important;
    }
}
</style>
@endsection 
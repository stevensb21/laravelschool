<div class="chat-message" style="margin-bottom: 14px; display: flex; align-items: flex-start;">
    <img src="{{ asset('images/man.jpg') }}" alt="avatar" style="width:36px; height:36px; border-radius:50%; margin-right:12px;">
    <div style="flex:1;">
        <div style="font-weight:600; color:var(--link-color); font-size:15px;">
            @php
                $user = $msg->user;
                if ($user) {
                    if ($user->role === 'teacher') {
                        $teacher = \App\Models\Teacher::where('users_id', $user->id)->first();
                        echo $teacher ? $teacher->fio : $user->name;
                    } elseif ($user->role === 'student') {
                        $student = \App\Models\Student::where('users_id', $user->id)->first();
                        echo $student ? $student->fio : $user->name;
                    } else {
                        echo $user->name;
                    }
                } else {
                    echo 'Пользователь';
                }
            @endphp
        </div>
        @if($msg->message)
            <div style="font-size:16px; color:var(--text-primary); margin-bottom:2px;">{{ $msg->message }}</div>
        @endif
        @if($msg->file_path)
            <div style="margin: 6px 0;">
                <a href="{{ \App\Helpers\FileHelper::getFileUrl($msg->file_path) }}" target="_blank" style="color:var(--btn-primary); text-decoration:underline; font-size:15px;">
                    <svg style="width:18px;height:18px;vertical-align:middle;margin-right:4px;fill:var(--btn-primary);" viewBox="0 0 24 24"><path d="M14.59,2.59C15.37,1.81 16.63,1.81 17.41,2.59L21.41,6.59C22.19,7.37 22.19,8.63 21.41,9.41L10.41,20.41C9.63,21.19 8.37,21.19 7.59,20.41L3.59,16.41C2.81,15.63 2.81,14.37 3.59,13.59L14.59,2.59M15,4L5,14V19H10L20,9L15,4Z" /></svg>
                    Вложение
                </a>
            </div>
        @endif
        <div style="font-size:12px; color:var(--text-muted);">{{ $msg->created_at->format('d.m.Y H:i') }}</div>
    </div>
</div>


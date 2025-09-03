<div class="chat-content-wrapper">
    <div class="chat-content" style="height: 400px; overflow-y: auto; padding-right: 1rem;">
        @forelse($chats as $chat)
            @if($chat->intUser_ID == auth()->id())
                {{-- Pesan dari "Saya" (rata kanan) --}}
                <div class="d-flex justify-content-end">
                    <div class="p-3 mb-2 rounded-4 text-white w-50" style="background-color: #435ebe;">
                        <p class="mb-0 text-break">{{ $chat->txtMessage }}</p>
                        <small class="text-white-50 d-block text-end mt-1">{{ $chat->created_at ? $chat->created_at->format('H:i') : '' }}</small>
                    </div>
                </div>
            @else
                {{-- Pesan dari "Orang Lain" (rata kiri) --}}
                <div class="d-flex justify-content-start">
                    <div class="bg-light p-3 mb-2 rounded-4 w-50">
                        <p class="mb-1 fw-bold">{{ $chat->user->txtFullName }} ({{ ucfirst($chat->user->roles->first()->name) }})</p>
                        <p class="mb-0 text-wrap">{{ $chat->txtMessage }}</p>
                        <small class="text-muted d-block text-end mt-1">{{ $chat->created_at ? $chat->created_at->format('H:i') : '' }}</small>
                    </div>
                </div>
            @endif
        @empty
            <p class="text-center text-muted">Belum ada percakapan.</p>
        @endforelse
    </div>
</div>

<div class="chat-form-wrapper">
    <form action="{{ route('submissions.chat.store', $submission) }}" method="POST" class="d-flex" id="chat-form-inside-modal">
        @csrf
        <input type="text" name="txtMessage" class="form-control me-2" placeholder="Ketik pesan Anda..." required>
        <button type="button" id="btn-send-chat" class="btn btn-primary">Kirim</button>
    </form>
</div>

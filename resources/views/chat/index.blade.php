@extends('layouts.admin')

@section('title', 'Live Chat')

@section('styles')
<style>
    .chat-layout {
        height: calc(100vh - 180px);
        min-height: 500px;
    }
    .chat-sidebar {
        background-color: #18181b !important;
        border-right: 1px solid #27272a !important;
        height: 100%;
        overflow-y: auto;
    }
    .chat-window {
        background-color: #121214 !important;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        background-color: #0b0c10 !important;
        border-bottom: 1px solid #27272a !important;
    }
    .message-bubble {
        max-width: 70%;
        padding: 0.8rem 1.2rem;
        border-radius: 1rem;
        margin-bottom: 1rem;
        word-wrap: break-word;
    }
    .message-sent {
        background: var(--primary-gradient) !important;
        color: #090a0f !important;
        border-bottom-right-radius: 0.2rem;
        font-weight: 500;
    }
    .message-received {
        background-color: #27272a !important;
        color: #f8fafc !important;
        border-bottom-left-radius: 0.2rem;
    }
    .chat-active-item {
        background-color: rgba(234, 179, 8, 0.08) !important;
        border-left: 3px solid #eab308 !important;
    }
    .chat-item:hover {
        background-color: rgba(255, 255, 255, 0.02);
    }
    .contact-item:hover {
        background-color: rgba(234, 179, 8, 0.05) !important;
        cursor: pointer;
    }
    
    /* Online/Offline Status Dots */
    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        transition: all 0.3s ease;
    }
    .status-online {
        background-color: #22c55e !important;
        box-shadow: 0 0 8px rgba(34, 197, 94, 0.7);
    }
    .status-offline {
        background-color: #ef4444 !important;
        box-shadow: 0 0 8px rgba(239, 68, 68, 0.7);
    }
</style>
@endsection

@section('content')
<div class="card card-premium border-0 p-0 overflow-hidden shadow-lg">
    <div class="row g-0 chat-layout">
        <!-- Sidebar Obrolan (Kolom Kiri) -->
        <div class="col-12 col-md-4 chat-sidebar d-flex flex-column">
            <!-- Header Pencarian & Tombol Obrolan Baru -->
            <div class="p-3 border-bottom border-dark d-flex align-items-center justify-content-between">
                <h5 class="m-0 fw-bold text-white"><i class="bi bi-chat-left-dots-fill text-primary me-2"></i>Obrolan</h5>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#newChatModal">
                    <i class="bi bi-plus-lg me-1"></i> Baru
                </button>
            </div>

            <!-- List Sesi Chat -->
            <div class="flex-grow-1 overflow-y-auto" style="list-style: none;">
                @forelse($chats as $c)
                    @php
                        // Tentukan info penerima (lawan bicara)
                        $opponent = ($c->mahasiswa_id == Auth::id()) ? $c->staff : $c->mahasiswa;
                        $lastMsg = $c->messages->last();
                    @endphp
                    <a href="{{ route('chat.index', ['id' => $c->id]) }}" class="text-decoration-none chat-item d-block p-3 border-bottom border-dark {{ ($activeChat && $activeChat->id == $c->id) ? 'chat-active-item' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold text-white mb-1 d-flex align-items-center">
                                    <span class="status-dot {{ $opponent && $opponent->isOnline() ? 'status-online' : 'status-offline' }} me-2"></span>
                                    {{ $opponent->name ?? 'Pengguna Terhapus' }}
                                </h6>
                                <span class="badge bg-secondary mb-2" style="font-size: 0.7rem;">
                                    @if($opponent->hasRole('mahasiswa')) Mahasiswa
                                    @elseif($opponent->hasRole('admin')) Admin
                                    @elseif($opponent->hasRole('staff')) Staff
                                    @else WD3 @endif
                                </span>
                                <p class="text-muted small mb-0 text-truncate" style="max-width: 200px;">
                                    {{ $lastMsg ? $lastMsg->message : 'Belum ada pesan.' }}
                                </p>
                            </div>
                            @if($lastMsg)
                                <small class="text-muted text-nowrap" style="font-size: 0.7rem;">{{ $lastMsg->sent_at->format('H:i') }}</small>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="text-center text-muted py-5 px-3">
                        <i class="bi bi-chat-square-text fs-2 d-block mb-3 text-secondary"></i>
                        <p class="small mb-0">Belum ada obrolan aktif. Klik tombol "Baru" untuk mulai chat.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Jendela Obrolan (Kolom Kanan) -->
        <div class="col-12 col-md-8 chat-window">
            @if($activeChat)
                @php
                    $activeOpponent = ($activeChat->mahasiswa_id == Auth::id()) ? $activeChat->staff : $activeChat->mahasiswa;
                @endphp
                <!-- Header Jendela Chat -->
                <div class="p-3 bg-dark border-bottom border-dark d-flex align-items-center">
                    <div class="bg-indigo text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: var(--primary-gradient);">
                        {{ strtoupper(substr($activeOpponent->name, 0, 1)) }}
                    </div>
                    <div>
                        <h6 class="fw-bold text-white m-0 d-flex align-items-center">
                            <span id="active-status-dot" class="status-dot {{ $activeOpponent->isOnline() ? 'status-online' : 'status-offline' }} me-2"></span>
                            {{ $activeOpponent->name }}
                        </h6>
                        <small class="text-muted">
                            @if($activeOpponent->hasRole('mahasiswa')) Mahasiswa
                            @elseif($activeOpponent->hasRole('admin')) Admin
                            @elseif($activeOpponent->hasRole('staff')) Staf Kemahasiswaan
                            @else Wakil Dekan 3 @endif
                        </small>
                    </div>
                </div>

                <!-- Kontainer Pesan -->
                <div class="chat-messages d-flex flex-column" id="chat-messages-container">
                    @foreach($activeChat->messages as $msg)
                        <div class="d-flex {{ $msg->sender_id == Auth::id() ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="message-bubble {{ $msg->sender_id == Auth::id() ? 'message-sent' : 'message-received' }}">
                                <div class="message-text">{{ $msg->message }}</div>
                                <div class="text-end small opacity-75 mt-1" style="font-size: 0.65rem;">
                                    {{ $msg->sent_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Formulir Input Chat -->
                <div class="p-3 bg-dark border-top border-dark">
                    <form id="chat-form" action="{{ route('chat.send', ['id' => $activeChat->id]) }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" id="chat-input" name="message" class="form-control rounded-start-pill py-2.5 px-4" placeholder="Ketik pesan Anda di sini..." autocomplete="off" required>
                            <button type="submit" class="btn btn-primary rounded-end-pill px-4 d-flex align-items-center justify-content-center">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <!-- Tampilan Kosong (Default) -->
                <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center text-center p-5 text-muted">
                    <div class="bg-dark p-4 rounded-circle mb-4 text-primary" style="box-shadow: 0 0 20px rgba(234, 179, 8, 0.15);">
                        <i class="bi bi-chat-dots fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2">Pilih Sesi Obrolan</h5>
                    <p class="small text-muted mb-0" style="max-width: 400px;">Silakan pilih percakapan di daftar obrolan sebelah kiri atau klik tombol "Baru" untuk memilih kontak baru.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- Modal Kontak Obrolan Baru -->
<div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom border-dark">
                <h5 class="modal-title text-white fw-bold" id="newChatModalLabel"><i class="bi bi-person-plus-fill text-primary me-2"></i>Mulai Obrolan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Kolom Pencarian Kontak -->
                <div class="p-3 border-bottom border-dark bg-dark">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 border-dark text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="contact-search" class="form-control border-start-0 border-dark" placeholder="Cari nama kontak...">
                    </div>
                </div>

                <!-- Daftar Kontak -->
                <div class="list-group list-group-flush bg-transparent" id="contacts-list">
                    @forelse($contacts as $contact)
                        <form action="{{ route('chat.start') }}" method="POST" class="contact-form">
                            @csrf
                            <input type="hidden" name="recipient_id" value="{{ $contact->id }}">
                            <button type="submit" class="list-group-item list-group-item-action bg-transparent border-dark px-4 py-3 contact-item text-start d-flex align-items-center">
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold" style="width: 38px; height: 38px; background: var(--primary-gradient) !important; color: #090a0f !important;">
                                    {{ strtoupper(substr($contact->name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-white mb-0 contact-name d-flex align-items-center">
                                        <span class="status-dot {{ $contact->isOnline() ? 'status-online' : 'status-offline' }} me-2"></span>
                                        {{ $contact->name }}
                                    </h6>
                                    <span class="badge bg-secondary mt-1" style="font-size: 0.65rem;">
                                        @if($contact->hasRole('mahasiswa')) Mahasiswa
                                        @elseif($contact->hasRole('admin')) Administrator
                                        @elseif($contact->hasRole('staff')) Staf Kemahasiswaan
                                        @else Wakil Dekan 3 @endif
                                    </span>
                                </div>
                                <i class="bi bi-chat-fill text-primary fs-5"></i>
                            </button>
                        </form>
                    @empty
                        <div class="text-center text-muted py-5 px-3">
                            <i class="bi bi-people-fill fs-2 d-block mb-2 text-secondary"></i>
                            <p class="small mb-0">Tidak ada kontak yang tersedia saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($activeChat)
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chatMessages = document.getElementById('chat-messages-container');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const activeChatId = "{{ $activeChat->id }}";
        const currentUserId = "{{ Auth::id() }}";

        // Scroll otomatis ke dasar pesan chat
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        scrollToBottom();

        // AJAX Send Message (Cegah reload halaman)
        chatForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const messageText = chatInput.value.trim();
            if (!messageText) return;

            // Bersihkan input secepatnya untuk responsivitas UI
            chatInput.value = '';

            const formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('message', messageText);

            fetch("{{ route('chat.send', ['id' => $activeChat->id]) }}", {
                method: "POST",
                body: formData,
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    appendMessage(data.message);
                    scrollToBottom();
                }
            })
            .catch(err => {
                console.error("Gagal mengirim pesan: ", err);
            });
        });

        // Tambah gelembung chat ke container
        function appendMessage(msg) {
            const isMe = msg.sender_id == currentUserId;
            
            // Format waktu sederhana
            const sentDate = new Date(msg.sent_at);
            const hours = String(sentDate.getHours()).padStart(2, '0');
            const minutes = String(sentDate.getMinutes()).padStart(2, '0');
            const timeStr = `${hours}:${minutes}`;

            const wrapper = document.createElement('div');
            wrapper.className = `d-flex ${isMe ? 'justify-content-end' : 'justify-content-start'}`;

            wrapper.innerHTML = `
                <div class="message-bubble ${isMe ? 'message-sent' : 'message-received'}">
                    <div class="message-text">${escapeHtml(msg.message)}</div>
                    <div class="text-end small opacity-75 mt-1" style="font-size: 0.65rem;">
                        ${timeStr}
                    </div>
                </div>
            `;
            chatMessages.appendChild(wrapper);
        }

        // Helper untuk mencegah serangan XSS
        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // AJAX Long-Polling: Cek pesan baru setiap 3 detik
        let lastMessageCount = chatMessages.children.length;

        function fetchNewMessages() {
            fetch(`/chat/${activeChatId}/messages`, {
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length > lastMessageCount) {
                    // Kosongkan container lalu render semua ulang atau append saja
                    chatMessages.innerHTML = '';
                    data.messages.forEach(msg => {
                        appendMessage(msg);
                    });
                    lastMessageCount = data.messages.length;
                    scrollToBottom();
                }
                
                // Perbarui status online/offline lawan bicara di header chat secara dinamis
                const activeDot = document.getElementById('active-status-dot');
                if (activeDot && data.hasOwnProperty('opponent_online')) {
                    if (data.opponent_online) {
                        activeDot.classList.remove('status-offline');
                        activeDot.classList.add('status-online');
                    } else {
                        activeDot.classList.remove('status-online');
                        activeDot.classList.add('status-offline');
                    }
                }
            })
            .catch(err => {
                console.error("Gagal sinkronisasi pesan: ", err);
            });
        }

        // Jalankan sinkronisasi berkala
        setInterval(fetchNewMessages, 3000);
    });
</script>
@endif

<script>
    // Fitur pencarian kontak di modal
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('contact-search');
        const contactsList = document.getElementById('contacts-list');
        const contactForms = contactsList.getElementsByClassName('contact-form');

        searchInput.addEventListener('input', function() {
            const query = searchInput.value.toLowerCase().trim();

            for (let i = 0; i < contactForms.length; i++) {
                const nameEl = contactForms[i].getElementsByClassName('contact-name')[0];
                if (nameEl) {
                    const name = nameEl.textContent.toLowerCase();
                    if (name.includes(query)) {
                        contactForms[i].style.setProperty('display', 'block', 'important');
                    } else {
                        contactForms[i].style.setProperty('display', 'none', 'important');
                    }
                }
            }
        });
    });
</script>
@endsection

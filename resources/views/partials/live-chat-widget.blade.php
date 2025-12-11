{{-- resources/views/partials/live-chat-widget.blade.php --}}

<style>
    .small-chat-input {
        font-size: 0.85rem;
        line-height: 1.4rem;
        height: 1.4rem;
        /* tinggi dasar kecil */
    }

    #chat-input:focus {
        box-shadow: none !important;
        outline: none !important;
    }

    /* Floating widget wrapper: SELALU pojok kanan bawah */
    #chat-widget {
        position: fixed;
        bottom: 16px;
        right: 16px;
        z-index: 1050;
    }

    /* Tombol chat bulat (posisi ikut parent) */
    #chat-widget-toggle {
        /* tidak perlu position fixed lagi */
    }

    /* Panel chat default (desktop) */
    #chat-widget-panel {
        width: 340px;
        max-height: 80vh;
    }

    /* AREA CHAT DEFAULT */
    #chat-box {
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
        height: 320px;
        overflow-y: auto;
        padding: 10px;
        background: #e5ddd5;
    }

    /* ========= RESPONSIVE: MOBILE ========= */
    @media (max-width: 576px) {

        /* Widget tetap di pojok kanan bawah, hanya sedikit naik biar nggak mentok */
        #chat-widget {
            bottom: 12px;
            right: 12px;
        }

        /* Panel chat lebih lebar di HP, tapi tetap nempel kanan */
        #chat-widget-panel {
            width: calc(100vw - 24px);
            /* full lebar - padding kiri/kanan */
            max-width: 100vw;
            max-height: 75vh;
            margin-bottom: 8px;
            /* jarak sedikit di atas tombol */
            border-radius: 16px;
            /* biar agak rounded */
        }

        /* Tinggi area chat menyesuaikan */
        #chat-box {
            height: calc(75vh - 120px);
            /* kira-kira: total tinggi panel - header - footer - input */
        }
    }
</style>


{{-- FLOATING CHAT WIDGET --}}
<div id="chat-widget">
    {{-- TOMBOL BUKA/TUTUP WIDGET --}}
    <button id="chat-widget-toggle"
        class="btn btn-success rounded-circle shadow-lg d-flex align-items-center justify-content-center"
        style="width: 52px; height: 52px;">
        <i class="bi bi-chat-dots-fill fs-5"></i>
    </button>

    {{-- PANEL CHAT (DEFAULT DISSEMBUNYIKAN) --}}
    <div id="chat-widget-panel" class="card shadow-lg border-0 d-none mt-2">
        {{-- HEADER ALA WHATSAPP --}}
        <div class="card-header bg-success text-white d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="me-2 rounded-circle bg-light text-success d-flex align-items-center justify-content-center"
                    style="width: 32px; height: 32px;">
                    <i class="bi bi-chat-dots"></i>
                </div>
                <div>
                    <div class="fw-semibold" style="font-size:0.95rem;">Konsultasi Online</div>
                    <small id="status-text" class="text-white-50" style="font-size:0.75rem;">
                        Mengambil status...
                    </small>
                </div>
            </div>
            <button type="button" id="chat-widget-close" class="btn btn-sm btn-outline-light border-0 p-0 ms-2">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="card-body p-0 d-flex flex-column" style="max-height: 70vh;">
            {{-- FORM NAMA & PESAN PERTAMA --}}
            <div id="name-form" class="p-3 border-bottom">
                <div class="mb-2">
                    <label for="guest-name" class="form-label mb-1" style="font-size:0.85rem;">Nama Anda</label>
                    <input type="text" id="guest-name" class="form-control form-control-sm"
                        placeholder="Masukkan nama">
                </div>

                <div class="mb-2">
                    <label for="first-message" class="form-label mb-1" style="font-size:0.85rem;">Pertanyaan
                        awal</label>
                    <textarea id="first-message" rows="3" class="form-control form-control-sm"
                        placeholder="Tulis pertanyaan atau konsultasi Anda di sini..."></textarea>
                </div>

                <div class="d-grid">
                    <button id="btn-start-chat" class="btn btn-success btn-sm">
                        <i class="bi bi-send me-1"></i> Mulai Chat
                    </button>
                </div>
            </div>

            {{-- WRAPPER CHAT (DISABLED AWAL) --}}
            <div id="chat-wrapper" class="d-none d-flex flex-column">
                {{-- AREA PESAN --}}
                <div id="chat-box">
                    {{-- pesan akan di-append via JS --}}
                </div>

                {{-- INPUT CHAT ALA WHATSAPP --}}
                <div class="border-top bg-light px-2 py-2">
                    <div class="d-flex align-items-center gap-2">
                        {{-- WRAPPER TEXTAREA --}}
                        <div class="flex-grow-1">
                            <div class="bg-white border rounded-pill px-3 py-1 d-flex align-items-center">
                                <textarea id="chat-input" rows="1" class="form-control border-0 shadow-none p-0 bg-transparent small-chat-input"
                                    style="resize:none; max-height: 96px; overflow-y:auto;" placeholder="Tulis pesan..."></textarea>
                            </div>
                        </div>

                        {{-- TOMBOL KIRIM BULAT --}}
                        <button id="btn-send"
                            class="btn btn-success rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;">
                            <i class="bi bi-send-fill" style="font-size:0.85rem;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    {{-- Kalau di layout sudah ada jQuery & bootstrap-icons, ini boleh dihapus --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <script>
        let chatToken = localStorage.getItem('live_chat_token');
        let lastMessageId = null;
        let pollingInterval = null;

        let isStartingChat = false;
        let isSendingMessage = false;
        let lastDateLabel = null;

        $(document).ready(function() {
            // BUKA WIDGET: tampilkan panel, sembunyikan tombol bulat
            $('#chat-widget-toggle').on('click', function() {
                $('#chat-widget-panel').removeClass('d-none');
                $('#chat-widget-toggle').addClass('d-none');

                if (chatToken) {
                    loadMessages(true);
                    startPolling();
                }
            });

            // TUTUP WIDGET: sembunyikan panel, munculkan lagi tombol bulat
            $('#chat-widget-close').on('click', function() {
                $('#chat-widget-panel').addClass('d-none');
                $('#chat-widget-toggle').removeClass('d-none');
            });


            // kalau sudah ada token, langsung masuk mode chat
            if (chatToken) {
                $('#name-form').addClass('d-none');
                $('#chat-wrapper').removeClass('d-none');
                loadMessages(true);
                startPolling();
            }

            $('#btn-start-chat').on('click', function() {
                startChat();
            });
            $('#btn-send').on('click', function() {
                sendMessage();
            });

            $('#chat-input').on('keypress', function(e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // auto-grow textarea
            $('#chat-input').on('input', function() {
                this.style.height = '1.4rem';
                this.style.height = this.scrollHeight + 'px';
            });

            // status admin
            loadStatus();
            setInterval(loadStatus, 30000);
        });

        function startChat() {
            const name = $('#guest-name').val().trim();
            const message = $('#first-message').val().trim();
            if (!name || !message) {
                alert('Nama dan pesan harus diisi');
                return;
            }

            if (isStartingChat) return;
            isStartingChat = true;
            $('#btn-start-chat').prop('disabled', true);

            $.post("{{ route('live-chat.start') }}", {
                name: name,
                message: message,
                _token: '{{ csrf_token() }}'
            }).done(function(res) {
                chatToken = res.token;
                localStorage.setItem('live_chat_token', chatToken);

                $('#name-form').addClass('d-none');
                $('#chat-wrapper').removeClass('d-none');

                $('#chat-box').empty();
                lastDateLabel = null;

                appendMessages([res.message]);
                lastMessageId = res.message.id;
                scrollToBottom();
                startPolling();
            }).fail(function() {
                alert('Gagal memulai chat');
            }).always(function() {
                isStartingChat = false;
                $('#btn-start-chat').prop('disabled', false);
            });
        }

        function sendMessage() {
            const message = $('#chat-input').val().trim();
            if (!message || !chatToken) return;

            if (isSendingMessage) return;
            isSendingMessage = true;
            $('#btn-send').prop('disabled', true);

            $.post("{{ route('live-chat.send') }}", {
                token: chatToken,
                message: message,
                _token: '{{ csrf_token() }}'
            }).done(function(res) {
                appendMessages([res]);
                lastMessageId = res.id;
                $('#chat-input').val('');
                scrollToBottom();

                const ta = document.getElementById('chat-input');
                if (ta) {
                    ta.style.height = '1.4rem';
                }
            }).fail(function() {
                alert('Gagal mengirim pesan');
            }).always(function() {
                isSendingMessage = false;
                $('#btn-send').prop('disabled', false);
            });
        }

        function loadMessages(initial = false) {
            if (!chatToken) return;

            const chatBoxEl = $('#chat-box')[0];
            let isAtBottom = true;

            if (chatBoxEl) {
                const distanceFromBottom = chatBoxEl.scrollHeight - (chatBoxEl.scrollTop + chatBoxEl.clientHeight);
                isAtBottom = distanceFromBottom < 50;
            }

            $.get("{{ route('live-chat.messages') }}", {
                token: chatToken
            }).done(function(res) {
                if (res.length > 0) {
                    $('#chat-box').empty();
                    lastDateLabel = null;

                    appendMessages(res);
                    lastMessageId = res[res.length - 1].id;

                    if (initial || isAtBottom) {
                        scrollToBottom();
                    }
                }
            }).fail(function(xhr) {
                if (xhr.status === 404) {
                    localStorage.removeItem('live_chat_token');
                    location.reload();
                }
            });
        }

        function appendMessages(messages) {
            messages.forEach(msg => {
                const isGuest = msg.sender_type === 'guest';
                const created = new Date(msg.created_at);

                const time = created.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const dateLabel = formatDateLabel(created);
                if (dateLabel !== lastDateLabel) {
                    $('#chat-box').append(`
                        <div style="text-align:center; margin:8px 0;">
                            <span class="badge bg-secondary" style="font-size: 0.75rem;">${dateLabel}</span>
                        </div>
                    `);
                    lastDateLabel = dateLabel;
                }

                let ticksHtml = '';
                if (isGuest) {
                    const isRead = !!msg.read_at;
                    ticksHtml = `
                        <span class="bi bi-check2-all"
                              style="margin-left:4px; color:${isRead ? '#0d6efd' : '#6c757d'};"></span>
                    `;
                }

                $('#chat-box').append(`
                    <div style="margin-bottom:6px; text-align:${isGuest ? 'right' : 'left'};">
                        <div style="
                            display:inline-block;
                            padding:6px 10px;
                            border-radius:8px;
                            max-width:80%;
                            text-align:left;
                            background:${isGuest ? '#d9fdd3' : '#ffffff'};
                        ">
                            <div>${msg.message.replace(/\n/g, '<br>')}</div>
                            <div style="font-size:10px; color:#666; margin-top:2px; text-align:right;">
                                ${time} ${isGuest ? ticksHtml : ''}
                            </div>
                        </div>
                    </div>
                `);
            });
        }

        function startPolling() {
            if (pollingInterval) return;
            pollingInterval = setInterval(function() {
                loadMessages();
            }, 3000);
        }

        function loadStatus() {
            $.get("{{ route('live-chat.status') }}")
                .done(function(res) {
                    if (res.online) {
                        $('#status-text').text('Online');
                    } else if (res.last_seen_human) {
                        $('#status-text').text('Terakhir dilihat ' + res.last_seen_human);
                    } else {
                        $('#status-text').text('Verifikator sedang offline');
                    }
                }).fail(function() {
                    $('#status-text').text('Status tidak tersedia');
                });
        }

        function formatDateLabel(dateObj) {
            const today = new Date();
            const pad = n => n.toString().padStart(2, '0');

            const todayYMD = `${today.getFullYear()}-${pad(today.getMonth() + 1)}-${pad(today.getDate())}`;
            const msgYMD = `${dateObj.getFullYear()}-${pad(dateObj.getMonth() + 1)}-${pad(dateObj.getDate())}`;

            const yesterday = new Date();
            yesterday.setDate(today.getDate() - 1);
            const yesterdayYMD = `${yesterday.getFullYear()}-${pad(yesterday.getMonth() + 1)}-${pad(yesterday.getDate())}`;

            if (msgYMD === todayYMD) return 'Hari ini';
            if (msgYMD === yesterdayYMD) return 'Kemarin';

            return dateObj.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function scrollToBottom() {
            const chatBox = $('#chat-box')[0];
            if (!chatBox) return;
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>
@endpush

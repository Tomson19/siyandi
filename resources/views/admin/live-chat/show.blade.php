@extends('layouts.bootstrap')

@section('content')
    <style>
        body{
        margin-top: -70px;
        overflow: hidden;
    }
        /* Hilangkan outline biru & shadow default di textarea input chat */
        .small-chat-input {
            font-size: 0.85rem;
            line-height: 1.4rem;
            height: 1.4rem;
            /* base kecil */
        }

        #admin-message:focus {
            box-shadow: none !important;
            outline: none !important;
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                {{-- CARD CHAT --}}
                <div class="card shadow-sm border-0">
                    {{-- HEADER ALA WHATSAPP --}}
                    <div class="card-header bg-success text-white d-flex align-items-center">
                        <a href="{{ route('admin.live-chat.index') }}" class="text-white me-3">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <div class="me-3 rounded-circle bg-light text-success d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <span class="fw-bold">{{ strtoupper(substr($consultation->guest_name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $consultation->guest_name }}</div>
                            {{-- status user (online / terakhir aktif) akan di-update via JS --}}
                            <small id="user-status-text" class="text-white-50">
                                Mengambil status...
                            </small>
                        </div>
                    </div>

                    <div class="card-body p-0 d-flex flex-column" style="height: 380px;">
                        {{-- AREA CHAT --}}
                        <div id="chat-box" class="flex-grow-1" style="overflow-y:auto; padding:10px; background:#e5ddd5;">
                            {{-- pesan akan di-load via AJAX --}}
                        </div>

                        <div class="border-top bg-light px-2 py-2">
                            <div class="d-flex align-items-center gap-2">
                                {{-- WRAPPER TEXTAREA --}}
                                <div class="flex-grow-1">
                                    <div class="bg-white border rounded-pill px-3 py-1 d-flex align-items-center">
                                        <textarea id="admin-message" rows="1"
                                            class="form-control border-0 shadow-none p-0 bg-transparent small-chat-input"
                                            style="resize:none; max-height: 96px; overflow-y:auto;" placeholder="Kirim Pesan"></textarea>
                                    </div>
                                </div>

                                {{-- TOMBOL KIRIM BULAT --}}
                                <button id="btn-reply"
                                    class="btn btn-success rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 36px; height: 36px;">
                                    <i class="bi bi-send-fill" style="font-size:0.9rem;"></i>
                                </button>
                            </div>
                        </div>


                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <script>
        const consultationId = {{ $consultation->id }};
        let lastMessageId = null;
        let pollingInterval = null;
        let isReplying = false;
        let lastDateLabel = null; // header tanggal

        $(document).ready(function() {
            loadMessages(true);
            startPolling();

            // ⬅️ INI YANG KEMARIN HILANG
            $('#btn-reply').on('click', function() {
                sendReply();
            });

            $('#admin-message').on('keypress', function(e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    sendReply();
                }
            });
        });

        function loadMessages(initial = false) {
            const url = "{{ route('admin.live-chat.messages', ':id') }}".replace(':id', consultationId);

            const chatBoxEl = $('#chat-box')[0];
            let isAtBottom = true;

            if (chatBoxEl) {
                const distanceFromBottom = chatBoxEl.scrollHeight - (chatBoxEl.scrollTop + chatBoxEl.clientHeight);
                isAtBottom = distanceFromBottom < 50;
            }

            $.get(url, {
                // ambil semua pesan untuk konsistensi tampilan
            }).done(function(res) {
                if (res.length > 0) {
                    $('#chat-box').empty();
                    lastDateLabel = null;

                    appendMessages(res);
                    lastMessageId = res[res.length - 1].id;

                    // update status user berdasarkan pesan terakhir dari guest
                    updateUserStatusFromMessages(res);

                    if (initial || isAtBottom) {
                        scrollToBottom();
                    }
                } else {
                    $('#user-status-text').text('Belum ada pesan');
                }
            });
        }

        function appendMessages(messages) {
            messages.forEach(msg => {
                // dari sudut pandang ADMIN:
                // verifikator = "saya" => kanan, hijau
                const isMine = msg.sender_type === 'verifikator';

                const created = new Date(msg.created_at);

                const time = created.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // ==== header tanggal (Hari ini / Kemarin / dd MMM yyyy) ====
                const dateLabel = formatDateLabel(created);
                if (dateLabel !== lastDateLabel) {
                    $('#chat-box').append(`
                        <div style="text-align:center; margin:8px 0;">
                            <span class="badge bg-secondary" style="font-size:0.75rem;">${dateLabel}</span>
                        </div>
                    `);
                    lastDateLabel = dateLabel;
                }

                // ✅ centang untuk PESAN YANG ADMIN KIRIM (isMine = true)
                let ticksHtml = '';
                if (isMine) {
                    const isReadByGuest = !!msg.read_by_guest_at; // null/undefined = false, string = true

                    ticksHtml = `
        <span class="bi bi-check2-all"
              style="margin-left:4px; color:${isReadByGuest ? '#0d6efd' : '#6c757d'};"></span>
    `;
                }


                $('#chat-box').append(`
                    <div style="margin-bottom:6px; text-align:${isMine ? 'right' : 'left'};">
                        <div style="
                            display:inline-block;
                            padding:6px 10px;
                            border-radius:14px;
                            max-width:80%;
                            background:${isMine ? '#d9fdd3' : '#ffffff'};
                            box-shadow:0 1px 1px rgba(0,0,0,.15);
                            text-align:left;
                        ">
                            <div>${msg.message.replace(/\n/g, '<br>')}</div>
                            <div style="font-size:10px; color:#666; margin-top:2px; text-align:right;">
                                ${time}
                                ${
                                    isMine
                                        ? `${ticksHtml} <span class="text-muted ms-1"></span>`
                                        : '<span class="text-muted ms-1"></span>'
                                }
                            </div>
                        </div>
                    </div>
                `);
            });
        }

        function scrollToBottom() {
            const chatBox = $('#chat-box')[0];
            if (!chatBox) return;
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function startPolling() {
            if (pollingInterval) return;
            pollingInterval = setInterval(() => {
                loadMessages();
            }, 3000);
        }

        function sendReply() {
            const message = $('#admin-message').val().trim();
            if (!message) return;

            if (isReplying) return;
            isReplying = true;
            $('#btn-reply').prop('disabled', true);

            const url = "{{ route('admin.live-chat.reply', ':id') }}".replace(':id', consultationId);

            $.post(url, {
                message: message,
                _token: $('meta[name="csrf-token"]').attr('content')
            }).done(function(res) {
                appendMessages([res]);
                lastMessageId = res.id;

                // kosongkan teks
                $('#admin-message').val('');

                // ⬅️ RESET TINGGI TEXTAREA SUPAYA TURUN LAGI
                const ta = document.getElementById('admin-message');
                if (ta) {
                    ta.style.height = '1.4rem'; // sama dengan base di CSS
                }

                scrollToBottom();
            }).fail(function() {
                alert('Gagal mengirim pesan');
            }).always(function() {
                isReplying = false;
                $('#btn-reply').prop('disabled', false);
            });
        }

        function autoResizeTextarea(el) {
            el.style.height = '1.4rem';
            el.style.height = el.scrollHeight + 'px';
        }

        // di ready:
        $('#admin-message').on('input', function() {
            autoResizeTextarea(this);
        });

        // di sendReply() setelah val(''):
        const ta = document.getElementById('admin-message');
        if (ta) {
            autoResizeTextarea(ta);
        }


        // ==== STATUS USER: Online / Terakhir aktif X menit lalu ====
        function updateUserStatusFromMessages(messages) {
            let lastGuestMsg = null;
            messages.forEach(m => {
                if (m.sender_type === 'guest') {
                    lastGuestMsg = m;
                }
            });

            if (!lastGuestMsg) {
                $('#user-status-text').text('Belum ada aktivitas');
                return;
            }

            const last = new Date(lastGuestMsg.created_at);
            const now = new Date();

            const diffMs = now - last;
            const diffMin = diffMs / 60000;

            if (diffMin <= 5) {
                $('#user-status-text').text('Online');
            } else {
                $('#user-status-text').text('Terakhir aktif ' + timeAgoIndo(last, now));
            }
        }

        function timeAgoIndo(date, now) {
            const diffMs = now - date;
            const diffSec = Math.floor(diffMs / 1000);
            const diffMin = Math.floor(diffSec / 60);
            const diffHour = Math.floor(diffMin / 60);
            const diffDay = Math.floor(diffHour / 24);

            if (diffMin < 1) return 'baru saja';
            if (diffMin < 60) return diffMin + ' menit yang lalu';
            if (diffHour < 24) return diffHour + ' jam yang lalu';
            if (diffDay === 1) return 'kemarin';
            return diffDay + ' hari yang lalu';
        }

        function formatDateLabel(dateObj) {
            const today = new Date();
            const pad = n => n.toString().padStart(2, '0');

            const todayYMD = `${today.getFullYear()}-${pad(today.getMonth() + 1)}-${pad(today.getDate())}`;
            const msgYMD = `${dateObj.getFullYear()}-${pad(dateObj.getMonth() + 1)}-${pad(dateObj.getDate())}`;

            const yesterday = new Date();
            yesterday.setDate(today.getDate() - 1);
            const yesterdayYMD = `${yesterday.getFullYear()}-${pad(yesterday.getMonth() + 1)}-${pad(yesterday.getDate())}`;

            if (msgYMD === todayYMD) {
                return 'Hari ini';
            }

            if (msgYMD === yesterdayYMD) {
                return 'Kemarin';
            }

            return dateObj.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        $(document).ready(function() {
            loadMessages(true);
            startPolling();

            $('#btn-reply').on('click', function() {
                sendReply();
            });

            $('#admin-message').on('keypress', function(e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    sendReply();
                }
            });

            // auto-grow tinggi textarea
            $('#admin-message').on('input', function() {
                this.style.height = '1.4rem'; // tinggi dasar (sesuai CSS)
                this.style.height = this.scrollHeight + 'px';
            });
        });
    </script>
@endpush

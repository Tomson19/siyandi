@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp

{{-- Tambah flex & max-height di card --}}
<div class="card shadow-sm border-0 d-flex flex-column"
     id="live-chat-card-inner"
     style="max-height: 75vh;">

    {{-- HEADER ALA WHATSAPP --}}
    <div class="card-header text-white d-flex flex-column p-0">
        <div class="d-flex align-items-center px-3 py-2" style="background:#075E54;">
            <i class="bi bi-whatsapp me-2"></i>
            <span class="fw-semibold">Konsultasi Masuk</span>
        </div>

        {{-- TAB FILTER ALA WA --}}
        <div class="bg-success-subtle px-2 py-1">
            <ul class="nav nav-pills small">
                <li class="nav-item">
                    <a href="{{ route('admin.live-chat.index', ['filter' => 'all']) }}"
                       class="nav-link px-3 py-1 {{ ($filter ?? 'all') === 'all' ? 'active' : '' }}">
                        Semua
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.live-chat.index', ['filter' => 'unread']) }}"
                       class="nav-link px-3 py-1 {{ ($filter ?? '') === 'unread' ? 'active' : '' }}">
                        Belum dibaca
                        @if(($unreadThreadsCount ?? 0) > 0)
                            <span class="badge bg-light text-success ms-1">
                                {{ $unreadThreadsCount }}
                            </span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- LIST CHAT: scroll di sini --}}
    <div class="list-group list-group-flush flex-grow-1"
         style="overflow-y: auto;">
        @forelse ($consultations as $c)
            @php
                $lastMessage = $c->messages()
                    ->latest('id')
                    ->first();

                $unreadCount = $c->messages()
                    ->where('sender_type', 'guest')
                    ->whereNull('read_at')
                    ->count();

                $lastTime = $c->last_message_at
                    ? Carbon::parse($c->last_message_at)
                    : null;
            @endphp

            <a href="{{ route('admin.live-chat.show', $c) }}"
               class="list-group-item list-group-item-action px-3 py-2">
                <div class="d-flex">
                    {{-- AVATAR HURUF PERTAMA --}}
                    <div class="me-3 d-flex align-items-center">
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                             style="width: 40px; height: 40px;">
                            <span class="fw-semibold">
                                {{ strtoupper(Str::substr($c->guest_name ?? '?', 0, 1)) }}
                            </span>
                        </div>
                    </div>

                    {{-- INFO CHAT --}}
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-semibold text-truncate" style="max-width: 70%;">
                                {{ $c->guest_name ?? 'Tamu' }}
                            </div>
                            <small class="text-muted ms-2">
                                @if($lastTime)
                                    {{ $lastTime->diffForHumans() }}
                                @else
                                    -
                                @endif
                            </small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-muted text-truncate me-2" style="max-width: 75%;">
                                @if($lastMessage)
                                    {{ Str::limit($lastMessage->message, 40) }}
                                @else
                                    Belum ada pesan
                                @endif
                            </small>

                            {{-- BADGE UNREAD ALA WA (per room) --}}
                            @if($unreadCount > 0)
                                <span class="badge rounded-pill bg-success">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="list-group-item text-center text-muted py-4">
                Belum ada konsultasi.
            </div>
        @endforelse
    </div>
</div>

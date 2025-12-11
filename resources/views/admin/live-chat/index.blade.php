@extends('layouts.bootstrap')

@section('content')
<style>
    body{
        margin-top: -70px;
        overflow: hidden;
    }
</style>
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            {{-- WRAPPER CARD YANG BISA DI-REPLACE AJAX --}}
            <div id="live-chat-card">
                @include('admin.live-chat._card')
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
        $(document).ready(function () {
            // interval polling: misal tiap 5 detik
            setInterval(function () {
                refreshLiveChatCard();
            }, 5000);
        });

        function refreshLiveChatCard() {
            const urlParams = new URLSearchParams(window.location.search);
            const filter = urlParams.get('filter') || 'all';

            const url = "{{ route('admin.live-chat.card') }}"
                + "?filter=" + encodeURIComponent(filter);

            $.get(url, function (html) {
                $('#live-chat-card').html(html);
            });
        }
    </script>
@endpush

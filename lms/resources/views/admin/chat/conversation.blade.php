@extends('layouts.admin')

@section('title', $student->name . ' ile Sohbet')
@section('page_title', $student->name . ' ile Sohbet')

@section('content')
    <div class="container-fluid">
        <div class="card mb-3">
            <div class="card-body" id="chat-messages" style="height: 400px; overflow-y: auto;">
                @include('admin.chat.partials.messages', ['messages' => $messages, 'student' => $student])
            </div>
        </div>

        <form id="chatForm" action="{{ route('admin.chat.send') }}" method="POST">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $student->id }}">
            <div class="input-group">
                <input type="text" name="message" class="form-control" placeholder="Mesajınızı yazın..." autocomplete="off" required>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Gönder
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        // Scroll en alta
        function scrollToBottom() {
            const container = document.getElementById('chat-messages');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }

        // Ajax ile mesajları çek
        function fetchMessages() {
            $.ajax({
                url: "{{ route('admin.chat.messages', $student->id) }}",
                type: "GET",
                success: function(data) {
                    $('#chat-messages').html(data);
                    scrollToBottom();
                },
                error: function() {
                    console.warn("Mesajlar alınamadı.");
                }
            });
        }

        // Sayfa ilk yüklendiğinde scroll en alta
        document.addEventListener('DOMContentLoaded', function () {
            scrollToBottom();
        });

        // 5 saniyede bir mesajları çek
        setInterval(fetchMessages, 1000);
    </script>
@endsection

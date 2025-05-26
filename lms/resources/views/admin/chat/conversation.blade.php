@extends('layouts.admin')

@section('title', $student->name . ' ile Sohbet')
@section('page_title', $student->name . ' ile Sohbet')

@section('content')
    <div class="container-fluid">
        <div class="card mb-3">
            <div class="card-body" style="height: 400px; overflow-y: auto;">
                @foreach($messages as $msg)
                    @php
                        $isAdmin = $msg->sender_type === \App\Models\User::class && $msg->sender_id === auth()->id();
                    @endphp
                    <div class="{{ $isAdmin ? 'text-end' : 'text-start' }}">
                        <strong>{{ $isAdmin ? 'Siz' : $student->name }}:</strong>
                        <div class="p-2 d-inline-block {{ $isAdmin ? 'bg-primary text-white' : 'bg-light' }}" style="border-radius: 10px; max-width: 70%;">
                            {{ $msg->message }}
                        </div>
                        <br>
                        <small class="text-muted">{{ $msg->created_at->format('d.m.Y H:i') }}</small>
                    </div>
                    <hr>
                @endforeach
            </div>
        </div>

        <form action="{{ route('admin.chat.send') }}" method="POST">
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

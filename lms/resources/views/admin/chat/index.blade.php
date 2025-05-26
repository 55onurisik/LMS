@extends('layouts.admin')

@section('title', $student->name . ' ile Sohbet')
@section('page_title', $student->name . ' ile Sohbet')

@section('content')
    <div class="container-fluid">
        <div class="card mb-3">
            <div class="card-body" style="max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                @foreach($messages as $msg)
                    <div class="{{ $msg->sender_id == auth()->id() ? 'text-end' : 'text-start' }}">
                        <strong>{{ $msg->sender_id == auth()->id() ? 'Siz' : $student->name }}:</strong> {{ $msg->message }}
                        <br>
                        <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                    </div>
                    <hr>
                @endforeach
            </div>
        </div>

        <form action="{{ route('admin.chat.send') }}" method="POST">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $student->id }}">
            <div class="input-group">
                <input type="text" name="message" class="form-control" placeholder="Mesajınızı yazın...">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-paper-plane"></i> Gönder
                </button>
            </div>
        </form>
    </div>
@endsection

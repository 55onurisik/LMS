@foreach($messages as $msg)
    @php
        $isAdmin = $msg->sender_type === \App\Models\User::class && $msg->sender_id === auth()->id();
    @endphp
    <div class="{{ $isAdmin ? 'text-end' : 'text-start' }} mb-2">
        <strong>{{ $isAdmin ? 'Siz' : $student->name }}:</strong>
        <div class="p-2 d-inline-block {{ $isAdmin ? 'bg-primary text-white' : 'bg-light' }}" style="border-radius: 10px; max-width: 70%;">
            {{ $msg->message }}
        </div>
        <br>
        <small class="text-muted">{{ $msg->created_at->format('d.m.Y H:i') }}</small>
    </div>
    <hr>
@endforeach

@extends('layouts.admin')

@section('title', 'Sohbet Listesi')
@section('page_title', 'Öğrencilerle Sohbet Başlat')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <ul class="list-group">
                    @foreach($students as $student)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $student->name }}
                            <a href="{{ route('admin.chat.with', $student->id) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-comments"></i> Sohbet Et
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection

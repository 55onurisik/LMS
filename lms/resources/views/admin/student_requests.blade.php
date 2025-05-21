@extends('layouts.admin')

@section('title', 'Öğrenci Kayıt Talepleri')
@section('page-title', 'Öğrenci Kayıt Talepleri')

@section('content')
    <div class="container mt-5">
        <h1>Öğrenci Kayıt Talepleri</h1>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($students->isEmpty())
            <p>Bekleyen kayıt bulunmamaktadır.</p>
        @else
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad Soyad</th>
                    <th>Telefon</th>
                    <th>Email</th>
                    <th>Sınıf</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
                </thead>
                <tbody>
                @foreach($students as $student)
                    <tr>
                        <td>{{ $student->id }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->phone }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->class_level }}</td>
                        <td>{{ ucfirst($student->status) }}</td>
                        <td>
                            @if($student->status === 'pending')
                                <form action="{{ route('admin.students.approve', $student->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Onayla</button>
                                </form>
                                <form action="{{ route('admin.students.reject', $student->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Reddet</button>
                                </form>
                            @else
                                <a href="{{ route('admin.students.schedule', $student->id) }}" class="btn btn-info btn-sm">Zamanlama Ayarla</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection

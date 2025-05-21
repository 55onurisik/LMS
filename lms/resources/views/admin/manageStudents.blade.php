@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrencileri Yönet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}"> <!-- Optional CSS -->
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4"></h1>
    <div class="card">
        <div class="card-header">
            Öğrenci Listesi
        </div>
        <div class="card-body">
            <div class="mb-3 d-flex justify-content-between flex-wrap gap-2">
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary">Yeni Öğrenci Ekle</a>
                <a href="{{ route('admin.students.requests') }}" class="btn btn-secondary">Öğrenci Kayıt Talepleri</a>
                <a href="{{ route('admin.schedule.overview') }}" class="btn btn-info">Genel Program Gör</a>
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>İsim</th>
                    <th>Email</th>
                    <th>İşlemler</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($students as $student)
                    <tr>
                        <td>{{ $student->id }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>
                            <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-warning btn-sm">Düzenle</a>
                            <a href="{{ route('admin.students.schedule', $student->id) }}" class="btn btn-info btn-sm">Saat Ayarla</a>
                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endsection

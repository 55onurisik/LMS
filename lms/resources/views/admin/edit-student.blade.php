<!-- resources/views/admin/edit-student.blade.php -->

@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('page-title', 'Dashboard')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Düzenle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Öğrenci Düzenle: {{ $student->name }}</div>

                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-sm float-end">
                    <i class="fa fa-arrow-left"></i> Geri
                </a><div class="card-body">
                    <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">İsim</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $student->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $student->email }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Şifre</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="text-muted">Şifreyi boş bırakmanız, mevcut şifreyi değiştirmeyecektir.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
@endsection

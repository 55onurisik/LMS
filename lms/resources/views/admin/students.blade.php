@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Öğrenci Listesi</h1>
        <div class="card">
            <div class="card-header">
                Öğrenci Listesi
            </div>
            <div class="card-body">
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
                                <a href="{{ route('student.topic.percentages', $student->id) }}" class="btn btn-info btn-sm">Konu Bazlı Yüzdeleri Gör</a>
                                <a href="{{ route('admin.students.exams', $student->id) }}" class="btn btn-primary btn-sm">Sınavları Gör</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

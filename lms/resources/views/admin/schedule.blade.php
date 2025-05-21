@extends('layouts.admin')

@section('title', 'Öğrenci Zamanlama')
@section('page-title', 'Öğrenci Zamanlama')

@section('content')
    <div class="container mt-5">
        <h1>{{ $student->name }} - Zamanlama Bilgileri</h1>
        <form action="{{ route('admin.students.schedule.save', $student->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="schedule_day" class="form-label">Gün</label>
                <select name="schedule_day" id="schedule_day" class="form-select" required>
                    <option value="">Seçiniz</option>
                    <option value="Pazartesi">Pazartesi</option>
                    <option value="Salı">Salı</option>
                    <option value="Çarşamba">Çarşamba</option>
                    <option value="Perşembe">Perşembe</option>
                    <option value="Cuma">Cuma</option>
                    <option value="Cumartesi">Cumartesi</option>
                    <option value="Pazar">Pazar</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="schedule_time" class="form-label">Saat</label>
                <input type="time" name="schedule_time" id="schedule_time" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </form>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Öğrenci Sınav Durumları')
@section('page-title', 'Öğrenci Sınav Durumları')

@section('content')
    <div class="container mt-5">
        <h1>{{ $student->name }} - Sınav Durumları</h1>
        <div class="card mt-4">
            <div class="card-header">
                Sınav Listesi
            </div>
            <div class="card-body">
                @if($exams->isEmpty())
                    <p>Sistemde herhangi bir sınav bulunmamaktadır.</p>
                @else
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Sınav Kodu</th>
                            <th>Sınav Adı</th>
                            <th>Sınav Tarihi</th>
                            <th>Durum</th>
                            <th>Detaylar</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($exams as $exam)
                            @php
                                // İlgili sınavda bu öğrenciye ait cevap var mı?
                                $solved = $exam->studentAnswers->where('student_id', $student->id)->count() > 0;
                            @endphp
                            <tr>
                                <td>{{ $exam->exam_code }}</td>
                                <td>{{ $exam->exam_title }}</td>
                                <td>{{ $exam->created_at->format('d-m-Y') }}</td>
                                <td>
                                    @if($solved)
                                        <span class="badge bg-success">Çözdü</span>
                                    @else
                                        <span class="badge bg-danger">Çözmedi</span>
                                    @endif
                                </td>
                                <td>
                                    @if($solved)
                                        <a href="{{ route('student.exam.details', [$student->id, $exam->id]) }}" class="btn btn-primary btn-sm">Detayları Gör</a>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection

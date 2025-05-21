@extends('layouts.admin')

@section('title', 'Sınav İstatistikleri')

@section('page-title', 'Sınav İstatistikleri')

@section('content')
    <div class="content-section">
        <div class="card">
            <div class="card-body">
                <!-- Geri Dön Butonu -->
                <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary mb-3">Geri Dön</a>

                <h5 class="card-title">Sınav İstatistikleri</h5>

                @if($statistics->isEmpty())
                    <div class="alert alert-info">
                        Bu sınav için henüz istatistik verisi bulunmamaktadır.
                    </div>
                @else
                    <table class="table table-hover">
                        <thead class="table-dark">
                        <tr>
                            <th scope="col">Öğrenci Adı</th>
                            <th scope="col">Öğrenci E-postası</th>
                            <th scope="col">Doğru Cevap</th>
                            <th scope="col">Yanlış Cevap</th>
                            <th scope="col">Boş Cevap</th>
                            <th scope="col">Net Puan</th>
                            <th scope="col">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($statistics as $statistic)
                            <tr>
                                <td>{{ $statistic['student']->name ?? 'N/A' }}</td>
                                <td>{{ $statistic['student']->email ?? 'N/A' }}</td>
                                <td>{{ $statistic['correct_count'] ?? 0 }}</td>
                                <td>{{ $statistic['wrong_count'] ?? 0 }}</td>
                                <td>{{ $statistic['blank_count'] ?? 0 }}</td>
                                <td>{{ number_format($statistic['net_score'] ?? 0, 2) }}</td>
                                <td>
                                    <a href="{{ route('student.exam.averages', ['studentId' => $statistic['student']->id ?? 0, 'examId' => $exam->id]) }}" class="btn btn-info btn-sm">Ortalama Gör</a>
                                    <a href="{{ route('student.exam.details', ['studentId' => $statistic['student']->id ?? 0, 'examId' => $exam->id]) }}" class="btn btn-primary btn-sm">Detayları Gör</a>
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

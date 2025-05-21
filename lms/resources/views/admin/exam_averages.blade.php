@extends('layouts.admin')

@section('title', 'Sınav İstatistikleri')

@section('page-title', 'Sınav İstatistikleri')

@section('content')
    <div class="content-section">
        <a href="{{ route('admin.exams.statistics', ['examId' => $exam->id]) }}" class="btn btn-secondary mb-3">Geri Dön</a>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Sınav İstatistikleri</h5>
                <table class="table table-hover">
                    <thead class="table-dark">
                    <tr>
                        <th scope="col">Konu</th>
                        <th scope="col">Öğrenci Yüzdesi</th>
                        <th scope="col">Ortalama Yüzde</th>
                        <th scope="col">Durum</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($topicAverages as $topicAverage)
                        <tr>
                            <td>{{ $topicAverage['topic_name'] }}</td>
                            <td class="percentage
                                    @if($topicAverage['student_average_correct'] < $topicAverage['average_correct'])
                                        below-average
                                    @elseif($topicAverage['student_average_correct'] > $topicAverage['average_correct'])
                                        above-average
                                    @else
                                        equal-average
                                    @endif">
                                {{ number_format($topicAverage['student_average_correct'], 2) }}%
                            </td>
                            <td class="percentage">
                                {{ number_format($topicAverage['average_correct'], 2) }}%
                            </td>
                            <td>
                                @if($topicAverage['student_average_correct'] < $topicAverage['average_correct'])
                                    <i class="fas fa-exclamation-circle warning-icon below-average"></i>
                                    Aşağıda
                                @elseif($topicAverage['student_average_correct'] > $topicAverage['average_correct'])
                                    <i class="fas fa-check-circle warning-icon above-average"></i>
                                    Yukarıda
                                @else
                                    <i class="fas fa-minus-circle warning-icon equal-average"></i>
                                    Aynı
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

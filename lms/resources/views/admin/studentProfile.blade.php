<!-- studentProfile.blade.php -->

@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('page-title', 'Dashboard')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Profili</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js kütüphanesini dahil ettik -->
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Öğrenci Profili - {{ $student->name }}</div>
                <div class="card-body">
                    <!-- Sınav Seçme Dropdown -->
                    <form method="GET" action="{{ route('student.exam.results', $student->id) }}">
                        <div class="mb-3">
                            <label for="exam_id" class="form-label">Sınav Seçin</label>
                            <select id="exam_id" name="exam_id" class="form-select" onchange="this.form.submit()">
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ $exam->id == $selectedExamId ? 'selected' : '' }}>
                                        {{ $exam->exam_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <!-- Grafik Butonu -->
                    <button id="showChart" class="btn btn-success mb-3">Grafiği Göster</button>

                    <!-- Grafik Alanı -->
                    <div id="chartContainer" style="display: none;">
                        <h5>Öğrenci Performansı Grafiği</h5>
                        <canvas id="performanceChart"></canvas>
                    </div>

                    <!-- Chart.js için JavaScript -->
                    <script>


                        document.getElementById('showChart').addEventListener('click', function() {
                            document.getElementById('chartContainer').style.display = 'block';
                            var ctx = document.getElementById('performanceChart').getContext('2d');
                            var performanceChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    datasets: [{
                                        label: 'Doğru Cevap Yüzdesi',
                                        data: {!! json_encode($topicPercentages) !!}, // Yüzdeler
                                        backgroundColor: {!! json_encode($backgroundColors) !!}, // Renkler
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            max: 100
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(tooltipItem) {
                                                    return tooltipItem.raw + '%';
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        });
                    </script>

                    <!-- Doğru ve Yanlış Cevaplar -->
                    @if($selectedExamId)
                        <!-- Doğru Cevaplar -->
                        <h5>Doğru Cevaplar</h5>
                        @if($correctAnswers->isNotEmpty())
                            <ul class="list-group mb-4">
                                @foreach($correctAnswers as $answer)
                                    <li class="list-group-item">
                                        Soru: {{ $answer->answer->question_text }} <br>
                                        Verdiğiniz Cevap: {{ $answer->answer->answer_text }} <br>
                                        Konu: {{ $answer->answer->topic->topic_name ?? 'Belirtilmemiş' }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>Bu sınav için doğru cevap bulunmuyor.</p>
                        @endif
                        <p>Sınav seçilmedi.</p>
                    @endif

                    <!-- Konu Bazlı Yüzdeler Butonu -->
                    <a href="{{ route('student.topic.percentages', $student->id) }}" class="btn btn-info mt-3">Konu Bazlı Yüzdeleri Gör</a>
                    <a href="{{ route('student.exam.averages', ['student' => $student->id, 'exam' => $selectedExamId]) }}" class="btn btn-primary">
                        Diğer Öğrencilerin Ortalamalarını Gör
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
@endsection

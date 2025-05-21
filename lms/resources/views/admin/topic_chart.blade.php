@extends('layouts.admin')

@section('title', 'Konu Başarı Grafiği')

@section('content')
    <div class="container mt-5">
        <!-- Geri butonu -->
        <a href="{{ route('student.topic.percentages', $student->id) }}" class="btn btn-secondary mb-3">Geri</a>

        <div class="card">
            <div class="card-header">{{ $topic->topic_name }} - Başarı Grafiği</div>
            <div class="card-body">
                @if(!empty($chartData))
                    <canvas id="topicChart"></canvas>
                @else
                    <p>Bu konuyla ilgili çözülmüş soru bulunmamaktadır.</p>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('topicChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line', // Çizgi grafik türü
                data: {
                    labels: {!! json_encode(array_column($chartData, 'exam_code')) !!},
                    datasets: [
                        {
                            label: '{{ $student->name }} Başarı Yüzdesi (%)',
                            data: {!! json_encode(array_column($chartData, 'percentage')) !!},
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.3,
                            fill: true,
                        },
                        {
                            label: 'Diğer Öğrencilerin Ortalaması (%)',
                            data: {!! json_encode(array_column($averageData, 'percentage')) !!},
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            tension: 0.3,
                            fill: true,
                        }
                    ]
                },
                options: {
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Sınav Kodları'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Başarı Yüzdesi (%)'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.parsed.y + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Genel Program Görüntüleri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        h1 {
            margin-bottom: 30px;
        }
        .pivot-table {
            width: 100%;
            border-collapse: collapse;
        }
        .pivot-table th, .pivot-table td {
            vertical-align: top;
            padding: 10px;
            border: 1px solid #dee2e6;
        }
        .pivot-table th {
            background-color: #343a40;
            color: white;
            text-align: center;
        }
        .student-info {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Genel Program Görüntüleri</h1>
    @php
        // Türkçe gün sıralamasını belirleyelim
        $days = ['Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi','Pazar'];
        // Öğrencilerden benzersiz saatleri toplayıp sıralayalım
        $times = $students->pluck('schedule_time')->unique()->sort();
    @endphp
    <table class="table pivot-table">
        <thead>
        <tr>
            <th>Saat / Gün</th>
            @foreach($days as $day)
                <th>{{ $day }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($times as $time)
            <tr>
                <td><strong>{{ $time }}</strong></td>
                @foreach($days as $day)
                    <td>
                        @php
                            // Bu hücre için ilgili öğrenci kayıtlarını filtreleyelim
                            $cellStudents = $students->filter(function($student) use ($day, $time) {
                                return $student->schedule_day === $day && $student->schedule_time === $time;
                            });
                        @endphp
                        @if($cellStudents->isEmpty())
                            &nbsp;
                        @else
                            @foreach($cellStudents as $s)
                                <div class="student-info">
                                    {{ $s->name }} ({{ $s->class_level }})
                                </div>
                            @endforeach
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

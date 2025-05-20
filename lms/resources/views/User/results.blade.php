<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Sınav: {{ $exam->exam_code }} Sonuçları</div>
                <div class="card-body">
                    @if (count($wrongAnswers) > 0)
                        <h5 class="mb-4">Yanlış Cevaplar</h5>
                        <ul class="list-group">
                            @foreach ($wrongAnswers as $wrongAnswer)
                                <li class="list-group-item">
                                    Soru {{ $wrongAnswer['question_number'] }}:
                                    <strong>Yanlış Cevap:</strong> {{ $wrongAnswer['submitted_answer'] }},
                                    <strong>Doğru Cevap:</strong> {{ $wrongAnswer['correct_answer'] }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>Tüm cevaplar doğru!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

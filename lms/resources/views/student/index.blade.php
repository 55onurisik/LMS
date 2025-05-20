<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mevcut Sınavlar</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Başarılı / Hata mesajları -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Mevcut Sınavlar</h4>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                <tr>
                    <th scope="col">Sınav Kodu</th>
                    <th scope="col">Sınav Başlığı</th>
                    <th scope="col">Soru Sayısı</th>
                    <th scope="col" style="width: 250px;">İşlemler</th>
                </tr>
                </thead>
                <tbody>
                @forelse($exams as $exam)
                    @php
                        // Şu anki giriş yapan öğrenci bu sınavı daha önce çözmüş mü?
                        $isSolved = \App\Models\StudentAnswer::where('exam_id', $exam->id)
                            ->where('student_id', auth()->id())
                            ->exists();
                    @endphp

                    <tr>
                        <td>{{ $exam->exam_code }}</td>
                        <td>{{ $exam->exam_title }}</td>
                        <td>{{ $exam->question_count }}</td>
                        <td>
                            @if($isSolved)
                                <!-- Öğrenci sınavı çözmüşse: -->
                                <span class="badge bg-secondary">
                                    <i class="fas fa-check-circle"></i> Sınav Çözüldü
                                </span>
                            @else
                                <!-- Öğrenci henüz çözmemişse: -->
                                <a href="{{ route('student.exams.answerForm', $exam->id) }}"
                                   class="btn btn-outline-primary btn-sm me-2">
                                    <i class="fas fa-edit"></i> Sınavı Çöz
                                </a>
                            @endif

                            <!-- Eğer görünürlük açıksa (review_visibility = true), 'Sonucu Gör' butonu -->
                            @if($exam->review_visibility)
                                <a href="{{ route('student.exams.review', $exam->id) }}"
                                   class="btn btn-outline-success btn-sm ms-2">
                                    <i class="fas fa-eye"></i> Sonucu Gör
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Henüz eklenmiş bir sınav yok.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- FontAwesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>

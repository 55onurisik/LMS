<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Değerlendirme - {{ $exam->exam_title }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">{{ $exam->exam_title }} - Değerlendirme</h4>
        </div>
        <div class="card-body">
            <!-- Geri Butonu -->
            <div class="mb-3">
                <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Geri Dön
                </a>
            </div>

            @if($studentAnswers->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Bu sınav için cevaplarınız bulunamadı.
                </div>
            @else
                <table class="table table-hover">
                    <thead class="table-dark">
                    <tr>
                        <th scope="col" style="width: 60px;">#</th>
                        <th scope="col">Öğrenci Cevabı</th>
                        <th scope="col">Doğru Cevap</th>
                        <th scope="col">Durum</th>
                        <th scope="col" style="width: 200px;">İnceleme</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($studentAnswers as $index => $sa)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $sa->students_answer }}</td>
                            <td>
                                @if($sa->answer)
                                    {{ $sa->answer->answer_text }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($sa->is_correct == 1)
                                    <span class="badge bg-success">Doğru</span>
                                @elseif($sa->is_correct == 0)
                                    <span class="badge bg-danger">Yanlış</span>
                                @elseif($sa->is_correct == 2)
                                    <span class="badge bg-warning">Boş</span>
                                @else
                                    <span class="badge bg-secondary">Tanımsız</span>
                                @endif
                            </td>
                            <td>
                                @if(!empty($sa->review_text) || !empty($sa->review_media))
                                    <button type="button"
                                            class="btn btn-info btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#reviewModal-{{ $sa->id }}">
                                        <i class="fas fa-eye"></i> İncele
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="reviewModal-{{ $sa->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">İnceleme #{{ $index + 1 }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @if($sa->review_text)
                                                        <p>{{ $sa->review_text }}</p>
                                                    @endif
                                                    @if($sa->review_media)
                                                        @php
                                                            // Veritabanında saklanan yol "exam_reviews/..." şeklinde
                                                            $mediaUrl = asset('storage/' . $sa->review_media);
                                                            $extension = strtolower(pathinfo($sa->review_media, PATHINFO_EXTENSION));
                                                        @endphp
                                                        @if(in_array($extension, ['mp4', 'mov', 'ogg', 'qt']))
                                                            <video controls width="100%">
                                                                <source src="{{ $mediaUrl }}" type="video/mp4">
                                                                Tarayıcınız video etiketini desteklemiyor.
                                                            </video>
                                                        @elseif(in_array($extension, ['jpeg','jpg','png','gif','svg']))
                                                            <img src="{{ $mediaUrl }}" alt="İnceleme Medyası" class="img-fluid">
                                                        @else
                                                            <p class="text-muted">Medya formatı desteklenmiyor.</p>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Kapat
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">İnceleme Yok</span>
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

<!-- Bootstrap JS ve Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- FontAwesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>

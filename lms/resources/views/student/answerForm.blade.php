<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Answers</title>
    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();

                successModal._element.addEventListener('hidden.bs.modal', function () {
                    window.location.href = "{{ route('student.dashboard') }}";
                });
            });
        </script>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Sınav: {{ $exam->exam_code }}</div>
                <div class="card-body">
                    <form id="examForm" action="{{ route('exams.submitStudentAnswers', $exam->id) }}" method="POST">
                        @csrf
                        <!-- Cevap Alanları -->
                        @foreach ($questions as $question)
                            <div class="d-flex align-items-center mb-3">
                                <!-- Soru Numarası -->
                                <span class="me-3">{{ $loop->iteration }}-</span>

                                <!-- Radio Button Seçenekleri -->
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="answers[{{ $question->id }}]"
                                           id="answer_{{ $question->id }}_blank"
                                           value="">
                                    <label class="form-check-label" for="answer_{{ $question->id }}_blank">Boş</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="answers[{{ $question->id }}]"
                                           id="answer_{{ $question->id }}_a"
                                           value="A">
                                    <label class="form-check-label" for="answer_{{ $question->id }}_a">A</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="answers[{{ $question->id }}]"
                                           id="answer_{{ $question->id }}_b"
                                           value="B">
                                    <label class="form-check-label" for="answer_{{ $question->id }}_b">B</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="answers[{{ $question->id }}]"
                                           id="answer_{{ $question->id }}_c"
                                           value="C">
                                    <label class="form-check-label" for="answer_{{ $question->id }}_c">C</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="answers[{{ $question->id }}]"
                                           id="answer_{{ $question->id }}_d"
                                           value="D">
                                    <label class="form-check-label" for="answer_{{ $question->id }}_d">D</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="answers[{{ $question->id }}]"
                                           id="answer_{{ $question->id }}_e"
                                           value="E">
                                    <label class="form-check-label" for="answer_{{ $question->id }}_e">E</label>
                                </div>
                            </div>
                        @endforeach

                        <button type="button" class="btn btn-primary" id="submitButton">
                            Cevapları Gönder
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Başarı Mesajı Modalı --}}
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bilgi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                {{ session('success') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                    Kapat
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (Popper Dahil) -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>

<script>
    document.getElementById('submitButton').addEventListener('click', function () {
        const form = document.getElementById('examForm');
        const inputs = form.querySelectorAll('input[type="radio"]');
        let allAnswered = true;

        // Sorulara verilen cevapları kontrol et
        const questions = [...new Set([...inputs].map(input => input.name))]; // Her sorunun name değerini al

        questions.forEach(question => {
            const selectedOption = form.querySelector(`input[name="${question}"]:checked`);
            if (!selectedOption) {
                allAnswered = false;
            }
        });

        if (!allAnswered) {
            alert("Lütfen tüm soruları cevaplayın!");
        } else {
            form.submit(); // Tüm sorular cevaplanmışsa formu gönder
        }
    });
</script>
</body>
</html>

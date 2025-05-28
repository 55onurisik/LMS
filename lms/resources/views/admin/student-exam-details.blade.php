@extends('layouts.admin')

@section('title', 'Sınav Detayları')
@section('page-title', 'Sınav Detayları')

@section('content')
    <div class="content-section">
        <div class="card">
            <div class="card-body">

                {{-- Üst Butonlar: Geri Dön (sol), Yapay Zeka Analiz (sağ) --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <a href="{{ route('admin.exams.statistics', ['examId' => $exam->id]) }}"
                           class="btn btn-secondary">
                            Geri Dön
                        </a>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button"
                                class="btn btn-warning"
                                id="yapayZekaAnalizBtn"
                                data-bs-toggle="modal"
                                data-bs-target="#yapayZekaAnalizModal">
                            Yapay Zeka Analiz
                        </button>
                    </div>
                </div>

                <h5 class="card-title">Sınav Detayları - {{ $student->name }}</h5>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-hover">
                    <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Konu</th>
                        <th scope="col">Doğru Cevap</th>
                        <th scope="col">Öğrenci Cevabı</th>
                        <th scope="col">Durum</th>
                        <th scope="col">İnceleme</th>
                        <th scope="col">İşlem</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($studentAnswers as $index => $studentAnswer)
                        @php
                            $qId = $studentAnswer->answer->id ?? null;
                            $existingReview = $examReviews->first(fn($r) =>
                                $r->question_id !== null && (int)$r->question_id === (int)$qId
                            );
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $studentAnswer->answer->topic->topic_name ?? 'N/A' }}</td>
                            <td>{{ $studentAnswer->answer->answer_text ?? 'N/A' }}</td>
                            <td>{{ $studentAnswer->students_answer ?? 'Boş' }}</td>
                            <td>
                                @if($studentAnswer->is_correct === 1)
                                    <span class="badge bg-success">Doğru</span>
                                @elseif($studentAnswer->is_correct === 0)
                                    <span class="badge bg-danger">Yanlış</span>
                                @elseif($studentAnswer->is_correct === 2)
                                    <span class="badge bg-warning">Boş</span>
                                @else
                                    <span class="badge bg-secondary">Tanımsız</span>
                                @endif
                            </td>
                            <td>
                                @if($existingReview)
                                    <span class="badge bg-success">Değerlendirildi</span>
                                @else
                                    <span class="badge bg-secondary">Değerlendirilmedi</span>
                                @endif
                            </td>
                            <td>
                                <button type="button"
                                        class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalSoru-{{ $studentAnswer->id }}">
                                    Değerlendir
                                </button>

                                <div class="modal fade"
                                     id="modalSoru-{{ $studentAnswer->id }}"
                                     tabindex="-1"
                                     aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('exam-reviews.store') }}"
                                                  method="POST"
                                                  enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Soru #{{ $index + 1 }} İnceleme</h5>
                                                    <button type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="exam_id"    value="{{ $exam->id }}">
                                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                    <input type="hidden" name="question_id"value="{{ $qId }}">

                                                    <div class="mb-3">
                                                        <label class="form-label">İnceleme Metni</label>
                                                        <textarea name="review_text"
                                                                  class="form-control"
                                                                  rows="4"
                                                                  required>{{ $existingReview->review_text ?? '' }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Video veya Resim Yükle</label>
                                                        <input type="file"
                                                               name="review_media"
                                                               accept="video/*,image/*"
                                                               class="form-control">
                                                    </div>
                                                    <div class="mb-3 form-check">
                                                        <input type="checkbox"
                                                               class="form-check-input"
                                                               id="broadcast_{{ $studentAnswer->id }}"
                                                               name="broadcast"
                                                               value="yes">
                                                        <label class="form-check-label"
                                                               for="broadcast_{{ $studentAnswer->id }}">
                                                            Diğer öğrenciler de bu incelemeyi alsın mı?
                                                        </label>
                                                    </div>
                                                    @if(!empty($existingReview->review_media))
                                                        <div class="mb-3">
                                                            <label class="form-label">Yüklü Medya</label>
                                                            <div>
                                                                <a href="{{ asset('storage/' . $existingReview->review_media) }}"
                                                                   target="_blank">
                                                                    Medyayı Görüntüle
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button"
                                                            class="btn btn-secondary"
                                                            data-bs-dismiss="modal">
                                                        İptal
                                                    </button>
                                                    <button type="submit"
                                                            class="btn btn-primary">
                                                        Kaydet
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    {{-- Yapay Zeka Analiz Modal --}}
    <div class="modal fade" id="yapayZekaAnalizModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yapay Zeka İle Performans Analizi</h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="aiAnalysisResult" class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                        <p>Analiz hazırlanıyor...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Modal açıldığında tetiklenecek
        var aiModalEl = document.getElementById('yapayZekaAnalizModal');
        aiModalEl.addEventListener('shown.bs.modal', function () {
            var container = document.getElementById('aiAnalysisResult');
            // İçerik hazırken spinner göster
            container.innerHTML = `
            <div class="spinner-border" role="status">
              <span class="visually-hidden">Yükleniyor...</span>
            </div>
            <p>Analiz hazırlanıyor...</p>
        `;
            // AI analiz isteği
            fetch("{{ route('admin.exams.aiAnalysis', ['exam' => $exam->id, 'student' => $student->id]) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        container.innerHTML = '<pre style="white-space: pre-wrap;">' + res.response + '</pre>';
                    } else {
                        container.innerHTML = '<div class="alert alert-danger">'
                            + (res.message || 'Analiz başarısız oldu.') + '</div>';
                    }
                })
                .catch(() => {
                    container.innerHTML = '<div class="alert alert-danger">Sunucu hatası. Lütfen tekrar deneyin.</div>';
                });
        });
    </script>
@endpush

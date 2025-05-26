    @extends('layouts.admin')

    @section('title', 'Admin Dashboard')

    @section('page-title', 'Dashboard')

    @section('content')
        <div class="content-section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Admin Paneline Hoş Geldiniz</h5>
                    <p class="card-text">Sol menüyü kullanarak işlemlerinizi yönetebilirsiniz.</p>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Toplam Öğrenci Sayısı</h5>
                            <p class="card-text">{{ $studentCount }} öğrenci kayıtlı.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Toplam Sınav Sayısı</h5>
                            <p class="card-text">{{ $examCount }} sınav mevcut.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yapay Zeka Destekli Analiz Bölümü -->
        </div>

        <!-- Analiz Modal -->
        <div class="modal fade" id="analysisModal" tabindex="-1" aria-labelledby="analysisModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="analysisModalLabel">Öğrenci Performans Analizi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="analysisContent">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Yükleniyor...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const analysisModal = document.getElementById('analysisModal');
                const analysisContent = document.getElementById('analysisContent');

                analysisModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const studentId = button.getAttribute('data-student-id');
                    
                    // Analiz verilerini getir
                    fetch(`/api/studentAPI/students/${studentId}/analysis`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const analysis = data.data;
                            let html = `
                                <h4 class="mb-4">${analysis.student_name}</h4>
                                <div class="alert alert-info">
                                    <strong>Genel Başarı Oranı:</strong> %${analysis.overall_success_rate}
                                </div>
                                <div class="topics-analysis">
                            `;

                            analysis.topics.forEach(topic => {
                                html += `
                                    <div class="card mb-3 ${topic.needs_review ? 'border-danger' : 'border-success'}">
                                        <div class="card-header">
                                            <h5 class="mb-0">${topic.topic_name}</h5>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Başarı Oranı:</strong> %${topic.success_rate}</p>
                                            <p><strong>Toplam Soru:</strong> ${topic.total_questions}</p>
                                            <p><strong>Doğru Cevap:</strong> ${topic.correct_answers}</p>
                                            <div class="recommendations mt-3">
                                                <h6>Öneriler:</h6>
                                                <ul>
                                                    ${topic.recommendations.map(rec => `<li>${rec}</li>`).join('')}
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });

                            // Yapay Zeka Analizi
                            if (analysis.ai_analysis) {
                                html += `
                                    <div class="card mt-4">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0">Yapay Zeka Analizi</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="ai-analysis">
                                                ${analysis.ai_analysis.split('\n').map(line => `<p>${line}</p>`).join('')}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }

                            html += '</div>';
                            analysisContent.innerHTML = html;
                        } else {
                            analysisContent.innerHTML = '<div class="alert alert-danger">Analiz verileri alınamadı.</div>';
                        }
                    })
                    .catch(error => {
                        analysisContent.innerHTML = '<div class="alert alert-danger">Bir hata oluştu.</div>';
                        console.error('Error:', error);
                    });
                });
            });
        </script>
        @endpush
    @endsection

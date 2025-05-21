@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Mevcut Sınavlar</h4>
            </div>
            <div class="card-body">
                <!-- Sınav Ekle Butonu (opsiyonel) -->
                <div class="mb-3 text-end">
                    <a href="{{ route('admin.exams.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Yeni Sınav Ekle
                    </a>
                </div>

                <!-- Başarı Mesajı (toggle sonrası) -->
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-hover">
                    <thead class="table-dark">
                    <tr>
                        <th scope="col">Sınav Adı</th>
                        <th scope="col">Sınav Kodu</th>
                        <th scope="col">Soru Sayısı</th>
                        <th scope="col">Değerlendirmeler</th>
                        <th scope="col" style="width: 300px;">İşlemler</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($exams as $exam)
                        <tr>
                            <td>{{ $exam->exam_title }}</td>
                            <td>{{ $exam->exam_code }}</td>
                            <td>{{ $exam->question_count }}</td>
                            <td>
                                <!-- Switch Toggle (Bootstrap 5) -->
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="toggleVisibility-{{ $exam->id }}"
                                        {{ $exam->review_visibility ? 'checked' : '' }}
                                        onclick="window.location='{{ route('admin.exams.toggleVisibility', $exam->id) }}'">
                                    <label class="form-check-label" for="toggleVisibility-{{ $exam->id }}">
                                        @if($exam->review_visibility)
                                            <span class="badge bg-success">Açık</span>
                                        @else
                                            <span class="badge bg-danger">Kapalı</span>
                                        @endif
                                    </label>
                                </div>
                            </td>
                            <td>
                                <!-- Düzenle Butonu -->
                                <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn btn-outline-info btn-sm me-2">
                                    <i class="fas fa-edit"></i> Düzenle
                                </a>

                                <!-- İstatistik Görüntüle Butonu -->
                                <a href="{{ route('admin.exams.statistics', $exam->id) }}" class="btn btn-outline-secondary btn-sm me-2">
                                    <i class="fas fa-chart-bar"></i> İstatistik
                                </a>

                                <!-- Silme Butonu -->
                                <form action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bu sınavı silmek istediğinizden emin misiniz?');">
                                        <i class="fas fa-trash"></i> Sil
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                @if($exams->isEmpty())
                    <div class="alert alert-info text-center">
                        Henüz eklenmiş bir sınav yok.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

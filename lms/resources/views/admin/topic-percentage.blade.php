@extends('layouts.admin')

@section('title', 'Öğrenci Konu Yüzdeleri')

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">Konu Yüzdeleri - {{ $student->name }}</div>
            <div class="card-body">
                {{-- Geri Dön Butonu --}}
                <a href="{{ route('admin.students.index') }}" class="btn btn-primary mb-3">Geri Dön</a>

                {{-- Filtre Formu --}}
                <form action="{{ route('student.topic.percentages', $student->id) }}" method="GET">
                    <div class="row">
                        {{-- Sınıf Seç --}}
                        <div class="col-md-3 mb-2">
                            <label for="class">Sınıf Seçiniz</label>
                            <select name="class" id="class" class="form-control">
                                <option value="">Hepsi</option>
                                <option value="9"  {{ request('class') == '9'  ? 'selected' : '' }}>9. Sınıf</option>
                                <option value="10" {{ request('class') == '10' ? 'selected' : '' }}>10. Sınıf</option>
                                <option value="11" {{ request('class') == '11' ? 'selected' : '' }}>11. Sınıf</option>
                                <option value="12" {{ request('class') == '12' ? 'selected' : '' }}>12. Sınıf</option>
                            </select>
                        </div>

                        {{-- Sınav Seç --}}
                        <div class="col-md-3 mb-2">
                            <label for="exam">Sınav Seçiniz</label>
                            <select name="exam" id="exam" class="form-control">
                                <option value="">Hepsi</option>
                                <option value="tyt" {{ request('exam') == 'tyt' ? 'selected' : '' }}>TYT</option>
                                <option value="ayt" {{ request('exam') == 'ayt' ? 'selected' : '' }}>AYT</option>
                            </select>
                        </div>

                        {{-- Filtrele Butonu --}}
                        <div class="col-md-3 mb-2 align-self-end">
                            <button type="submit" class="btn btn-primary">
                                Filtrele
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Konu Listesi --}}
                <ul class="list-group mt-4">
                    @foreach($allTopics as $topic)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $topic->topic_name }}</strong>
                                @if(isset($topics[$topic->id]))
                                    <p class="mb-1">
                                        {{ number_format($topics[$topic->id]['percentage'], 2) }}% başarı
                                    </p>
                                @else
                                    <p class="mb-1">Bu konuda veri bulunmuyor.</p>
                                @endif
                            </div>
                            @if(isset($topics[$topic->id]))
                                <a href="{{ route('admin.topic.chart', ['studentId' => $student->id, 'topicId' => $topic->id]) }}"
                                   class="btn btn-info">
                                    Grafik Gör
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- JavaScript Kısmı --}}
    <script>
        // Exam seçilince Class'ı sıfırla ve devre dışı bırak
        document.getElementById('exam').addEventListener('change', function() {
            var examValue = this.value;
            var classSelect = document.getElementById('class');

            if (examValue !== '') {
                // Sınav seçilmişse sınıfı sıfırla ve devre dışı bırak
                classSelect.value = '';
                classSelect.disabled = true;
            } else {
                // Sınav seçilmediyse sınıf tekrar aktif olsun
                classSelect.disabled = false;
            }
        });

        // Class seçilince Exam'i sıfırla ve devre dışı bırak
        document.getElementById('class').addEventListener('change', function() {
            var classValue = this.value;
            var examSelect = document.getElementById('exam');

            if (classValue !== '') {
                // Sınıf seçilmişse sınavı sıfırla ve devre dışı bırak
                examSelect.value = '';
                examSelect.disabled = true;
            } else {
                // Sınıf seçilmediyse sınav tekrar aktif olsun
                examSelect.disabled = false;
            }
        });
    </script>
@endsection

@extends('layouts.admin')

@section('title', 'Konu Ekle')
@section('page-title', 'Konu Ekle')

@section('content')
    <div class="container mt-4">
        @if(session('success_topic'))
            <div class="alert alert-success">{{ session('success_topic') }}</div>
        @endif

        <div class="card">
            <div class="card-header">Konu Ekle</div>
            <div class="card-body">
                <form method="POST" action="{{ route('unit-topic.store-topic') }}">
                    @csrf

                    {{-- 1) Sınıf Seviyesi --}}
                    <div class="mb-3">
                        <label for="class_level_topic" class="form-label">Sınıf Seviyesi</label>
                        <select id="class_level_topic" name="class_level_topic" class="form-select" required>
                            <option value="">Seçiniz</option>
                            @foreach($classLevels as $lvl)
                                <option value="{{ $lvl }}"
                                    @selected($oldClassLevel == $lvl)>
                                    {{ $lvl }}. sınıf
                                </option>
                            @endforeach
                        </select>
                        @error('class_level_topic')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>

                    {{-- 2) Ünite --}}
                    <div class="mb-3">
                        <label for="unit_id_topic" class="form-label">Ünite</label>
                        <select id="unit_id_topic" name="unit_id" class="form-select" required>
                            <option value="">Önce sınıf seçin</option>
                            @foreach($allUnits as $unit)
                                <option value="{{ $unit->id }}"
                                        data-class-level="{{ $unit->class_level }}"
                                    @selected($oldUnitId == $unit->id)>
                                    {{ $unit->unit_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>

                    {{-- 3) Konu Adı --}}
                    <div class="mb-3">
                        <label for="topic_name" class="form-label">Konu Adı</label>
                        <input
                            type="text"
                            id="topic_name"
                            name="topic_name"
                            class="form-control"
                            placeholder="Konu adı"
                            value="{{ old('topic_name') }}"
                            required
                        >
                        @error('topic_name')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Konu Ekle</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Ünite verilerini JavaScript'e aktar --}}
    <div id="units-data"
         data-units='@json($allUnits->map(fn($u) => ['id' => $u->id, 'class_level' => $u->class_level, 'name' => $u->unit_name]))'
         style="display: none;"></div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classSelect = document.getElementById('class_level_topic');
            const unitSelect = document.getElementById('unit_id_topic');
            const unitsData = JSON.parse(document.getElementById('units-data').dataset.units);

            // Sınıf seçimi değiştiğinde
            classSelect.addEventListener('change', function() {
                const selectedClass = this.value;

                // Tüm üniteleri temizle
                unitSelect.innerHTML = '<option value="">Önce sınıf seçin</option>';

                if(selectedClass) {
                    // Filtrelenmiş üniteleri bul
                    const filteredUnits = unitsData.filter(unit =>
                        unit.class_level == selectedClass
                    );

                    // Yeni option'ları oluştur
                    filteredUnits.forEach(unit => {
                        const option = document.createElement('option');
                        option.value = unit.id;
                        option.textContent = unit.name;
                        unitSelect.appendChild(option);
                    });

                    // Eski seçimi geri yükle
                    @if($oldUnitId)
                        unitSelect.value = {{ $oldUnitId }};
                    @endif
                }
            });

            // Sayfa yüklendiğinde eski seçimi tetikle
            @if($oldClassLevel)
            classSelect.dispatchEvent(new Event('change'));
            @endif
        });
    </script>
@endpush

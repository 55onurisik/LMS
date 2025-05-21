@extends('layouts.admin')

@section('title', 'Sınav Düzenle')
@section('page-title', 'Sınav Düzenle')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4>Sınav Düzenle: {{ $exam->exam_title }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.exams.update', $exam->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Sınav Başlığı -->
                    <div class="mb-3">
                        <label for="exam_title" class="form-label">Sınav Başlığı</label>
                        <input type="text" name="exam_title" id="exam_title" class="form-control"
                               value="{{ old('exam_title', $exam->exam_title) }}" required>
                    </div>

                    <h5>Sorular</h5>
                    @foreach($questions as $index => $question)
                        <div class="mb-3">
                            <label class="form-label">Soru #{{ $question->question_number }}</label>
                            <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question->id }}">

                            <!-- Radio Button Grupları -->
                            <div class="d-flex align-items-center">
                                @php
                                    // Modeldeki cevap büyük harf olabilir, normalize edip küçük harfe çeviriyoruz.
                                    $selected = old("questions.{$index}.answer_text", strtolower($question->answer_text));
                                @endphp
                                @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio"
                                               name="questions[{{ $index }}][answer_text]"
                                               id="question_{{ $question->id }}_{{ $option }}"
                                               value="{{ $option }}"
                                               {{ $selected === $option ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="question_{{ $question->id }}_{{ $option }}">
                                            {{ strtoupper($option) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </form>
            </div>
        </div>
    </div>
@endsection

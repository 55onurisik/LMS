@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('page-title', 'Dashboard')

@section('content')
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınav Oluştur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .inline-select {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 10px;
        }
        .inline-radio {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-left: 20px;
        }
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .form-header {
            display: flex;
            gap: 20px;
            align-items: flex-end;
            margin-bottom: 20px;
        }
        .question {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <form id="exam-info-form" method="POST" action="{{ route('admin.exams.store') }}">
                @csrf
                <div class="form-section">
                    <div class="form-header">
                        <div class="mb-3">
                            <label for="exam_title" class="form-label">Sınav Başlığı</label>
                            <input type="text" class="form-control" id="exam_title" name="exam_title" required>
                        </div>
                        <div class="mb-3">
                            <label for="question_count" class="form-label">Soru Sayısı</label>
                            <input type="number" class="form-control" id="question_count" name="question_count" min="1" required>
                        </div>
                        <div class="mb-3">
                            <button type="button" id="generate-questions-btn" class="btn btn-success">Tamam</button>
                        </div>
                    </div>
                </div>

                <div class="form-section" id="bulk-selection-section" style="display: none;">
                    <h3>Toplu Seçim</h3>
                    <div class="inline-select">
                        <label for="bulk_class" class="form-label">Sınıf:</label>
                        <select class="form-select" id="bulk_class" name="bulk_class">
                            <option value="">Seçin</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>

                        <label for="bulk_unit" class="form-label">Ünite:</label>
                        <select class="form-select" id="bulk_unit" name="bulk_unit">
                            <option value="">Seçin</option>
                        </select>

                        <label for="bulk_topic" class="form-label">Konu:</label>
                        <select class="form-select" id="bulk_topic" name="bulk_topic">
                            <option value="">Seçin</option>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Sorular</h3>
                    <div id="questions-container">
                        <!-- Sorular dinamik olarak buraya eklenecek -->
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Sınavı Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#exam-info-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: '{{ route("admin.exams.store") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    window.location.href = '{{ route("admin.exams.index") }}';
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Hata:', textStatus, errorThrown);
                }
            });
        });

        // "Tamam" butonuna tıklanınca soruları oluştur
        $('#generate-questions-btn').on('click', function() {
            var count = $('#question_count').val();
            if(count > 0) {
                generateQuestionFields(count);
                toggleBulkSelection(count);
            } else {
                alert("Lütfen geçerli bir soru sayısı giriniz.");
            }
        });

        function toggleBulkSelection(count) {
            if (count > 0) {
                $('#bulk-selection-section').show();
            } else {
                $('#bulk-selection-section').hide();
            }
        }

        function generateQuestionFields(count) {
            var container = $('#questions-container');
            container.empty();

            for (var i = 1; i <= count; i++) {
                container.append(`
                <div class="question" id="question_${i}">
                    <div class="inline-select">
                        <h5>Soru ${i}</h5>
                        <label for="class_${i}" class="form-label">Sınıf:</label>
                        <select class="form-select class-select" id="class_${i}" name="questions[${i - 1}][class_id]" required>
                            <option value="">Seçin</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>

                        <label for="unit_${i}" class="form-label">Ünite:</label>
                        <select class="form-select unit-select" id="unit_${i}" name="questions[${i - 1}][unit_id]" required>
                            <option value="">Seçin</option>
                        </select>

                        <label for="topic_${i}" class="form-label">Konu:</label>
                        <select class="form-select topic-select" id="topic_${i}" name="questions[${i - 1}][topic_id]" required>
                            <option value="">Seçin</option>
                        </select>

                        <div class="inline-radio">
                            <label><input type="radio" name="questions[${i - 1}][answer_text]" value="A" required> A</label>
                            <label><input type="radio" name="questions[${i - 1}][answer_text]" value="B"> B</label>
                            <label><input type="radio" name="questions[${i - 1}][answer_text]" value="C"> C</label>
                            <label><input type="radio" name="questions[${i - 1}][answer_text]" value="D"> D</label>
                            <label><input type="radio" name="questions[${i - 1}][answer_text]" value="E"> E</label>
                        </div>
                    </div>
                </div>
            `);
            }
        }

        // Bulk seçim event'ları
        $('#bulk_class').on('change', function() {
            var classId = $(this).val();
            updateAllQuestions('class', classId);
            loadUnits(classId, 'bulk');
        });

        $('#bulk_unit').on('change', function() {
            var unitId = $(this).val();
            updateAllQuestions('unit', unitId);
            loadTopics(unitId, 'bulk');
        });

        $('#bulk_topic').on('change', function() {
            var topicId = $(this).val();
            updateAllQuestions('topic', topicId);
        });

        function updateAllQuestions(field, value) {
            $('.' + field + '-select').each(function() {
                $(this).val(value).trigger('change');
            });
        }

        // Tek tek seçim event'ları
        $(document).on('change', '.class-select', function() {
            var classId = $(this).val();
            var id = $(this).attr('id'); // Örneğin "class_1"
            var index = id.split('_')[1];
            loadUnits(classId, index);
        });

        $(document).on('change', '.unit-select', function() {
            var unitId = $(this).val();
            var id = $(this).attr('id'); // Örneğin "unit_1"
            var index = id.split('_')[1];
            loadTopics(unitId, index);
        });

        function loadUnits(classId, prefix) {
            $.ajax({
                url: '/api/units',
                method: 'GET',
                data: { class_id: classId },
                success: function(data) {
                    if(prefix === 'bulk') {
                        let unitSelect = $('#bulk_unit');
                        unitSelect.empty().append('<option value="">Seçin</option>');
                        if (data.units) {
                            $.each(data.units, function(index, unit) {
                                unitSelect.append('<option value="' + unit.id + '">' + unit.unit_name + '</option>');
                            });
                        }
                        $('.unit-select').each(function() {
                            $(this).empty().append('<option value="">Seçin</option>');
                            if (data.units) {
                                $.each(data.units, function(index, unit) {
                                    $(this).append('<option value="' + unit.id + '">' + unit.unit_name + '</option>');
                                }.bind(this));
                            }
                        });
                    } else {
                        let unitSelect = $('#unit_' + prefix);
                        unitSelect.empty().append('<option value="">Seçin</option>');
                        if (data.units) {
                            $.each(data.units, function(index, unit) {
                                unitSelect.append('<option value="' + unit.id + '">' + unit.unit_name + '</option>');
                            });
                        }
                    }
                }
            });
        }

        function loadTopics(unitId, prefix) {
            $.ajax({
                url: '/api/topics',
                method: 'GET',
                data: { unit_id: unitId },
                success: function(data) {
                    if(prefix === 'bulk') {
                        let topicSelect = $('#bulk_topic');
                        topicSelect.empty().append('<option value="">Seçin</option>');
                        if (data.topics) {
                            $.each(data.topics, function(index, topic) {
                                topicSelect.append('<option value="' + topic.id + '">' + topic.topic_name + '</option>');
                            });
                        }
                        $('.topic-select').each(function() {
                            $(this).empty().append('<option value="">Seçin</option>');
                            if (data.topics) {
                                $.each(data.topics, function(index, topic) {
                                    $(this).append('<option value="' + topic.id + '">' + topic.topic_name + '</option>');
                                }.bind(this));
                            }
                        });
                    } else {
                        let topicSelect = $('#topic_' + prefix);
                        topicSelect.empty().append('<option value="">Seçin</option>');
                        if (data.topics) {
                            $.each(data.topics, function(index, topic) {
                                topicSelect.append('<option value="' + topic.id + '">' + topic.topic_name + '</option>');
                            });
                        }
                    }
                }
            });
        }
    });
</script>
</body>
</html>
@endsection

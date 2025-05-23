<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('exam_code'); // Sınav kodu
            $table->integer('question_count')->default(0);
            $table->string('exam_title'); // Sınav adı
            //$table->date('exam_date'); // Sınav tarihi
            //$table->string('exam_type')->nullable(); // Sınav türü (örn: eYDS, YDS)
            //$table->integer('score')->nullable(); // Sınav skoru
            //$table->foreignId('user_id')->constrained()->onDelete('cascade'); // İlişkili kullanıcı
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};

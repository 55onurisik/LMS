<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('exam_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('question_id')->nullable();
            $table->text('review_text')->nullable();
            $table->timestamps();

            // Foreign key örnekleri (isteğe bağlı)
            // $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            // $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            // $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_reviews');
    }
}


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
        Schema::create('answers', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->foreignId('exam_id')->constrained()->onDelete('cascade'); // Foreign key to exams table
            $table->unsignedInteger('question_number'); // The number of the question
            $table->enum('answer_text', ['A', 'B', 'C', 'D', 'E']); // The correct answer
            $table->unsignedBigInteger('topic_id')->nullable()->constrained('topics')->onDelete('set null');
            $table->timestamps(); // Created at and Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->dropColumn('topic_id');
        });
    }
};

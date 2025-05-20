<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviewVisibilityToExamsTable extends Migration
{
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            // Boolean veya tinyInteger
            $table->boolean('review_visibility')->default(false)->after('question_count');
            // "false" yani başlangıçta kapalı
        });
    }

    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('review_visibility');
        });
    }
}

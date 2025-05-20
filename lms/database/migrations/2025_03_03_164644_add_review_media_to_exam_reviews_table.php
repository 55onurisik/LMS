<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviewMediaToExamReviewsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('exam_reviews', function (Blueprint $table) {
            // 'review_text' kolonundan sonra ekleniyor, isteğe bağlı olarak konumlandırabilirsiniz
            $table->string('review_media')->nullable()->after('review_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('exam_reviews', function (Blueprint $table) {
            $table->dropColumn('review_media');
        });
    }
}

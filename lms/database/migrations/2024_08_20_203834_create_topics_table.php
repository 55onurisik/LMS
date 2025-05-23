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
        Schema::create('topics', function (Blueprint $table) {
            $table->id(); // Otomatik olarak birincil anahtar id sütunu
            $table->string('topic_name'); // Konu adı
            $table->foreignId('unit_id')->constrained()->onDelete('cascade'); // Unite ile ilişkili foreign key
            $table->timestamps(); // created_at ve updated_at sütunları
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};

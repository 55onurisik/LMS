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
        Schema::create('units', function (Blueprint $table) {
            $table->id(); // Otomatik olarak birincil anahtar id sütunu
            $table->string('unit_name'); // Ünitenin adı
            $table->string('class_level'); // Sınıf seviyesi (örneğin 9, 10, 11, 12)
            $table->timestamps(); // created_at ve updated_at sütunları
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};

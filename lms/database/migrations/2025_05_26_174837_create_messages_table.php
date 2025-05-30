<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->morphs('sender');    // → sender_id + sender_type
            $table->morphs('receiver');  // → receiver_id + receiver_type

            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
}

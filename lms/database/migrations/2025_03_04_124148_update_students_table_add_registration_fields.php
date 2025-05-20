<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStudentsTableAddRegistrationFields extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->enum('class_level', ['9', '10', '11', '12', 'Mezun'])->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->string('schedule_day')->nullable();
            $table->time('schedule_time')->nullable();
            //asd
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('students', 'class_level')) {
                $table->dropColumn('class_level');
            }
            if (Schema::hasColumn('students', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('students', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            if (Schema::hasColumn('students', 'schedule_week')) {
                $table->dropColumn('schedule_week');
            }
            if (Schema::hasColumn('students', 'schedule_day')) {
                $table->dropColumn('schedule_day');
            }
            if (Schema::hasColumn('students', 'schedule_time')) {
                $table->dropColumn('schedule_time');
            }
        });
    }

}

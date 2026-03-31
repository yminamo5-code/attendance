<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreaktimeApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('breaktime_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_application_id')->constrained('attendance_applications')->cascadeOnDelete();
            $table->time('break_start');
            $table->time('break_end')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('breaktime_applications');
    }
}
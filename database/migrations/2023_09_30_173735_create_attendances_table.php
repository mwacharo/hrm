<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->date('attendance_date');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('clock_in_time');
            $table->string('clock_out_time')->nullable();
            $table->integer('hours_worked')->default(9);
            $table->string('status');
            $table->boolean('is_present')->default(false);
            $table->text('notes')->nullable();
            $table->decimal('overtime_hours', 8, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}

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
        Schema::create('goals', function (Blueprint $table) {
            $table->foreignId('goal_type_id')->nullable()->contrained()->onDelete('cascade');
            $table->string('subject');
            $table->string('target');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->integer('progress');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};

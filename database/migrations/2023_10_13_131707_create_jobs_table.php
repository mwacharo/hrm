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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('location');
            $table->integer('vacancies');
            $table->integer('experience');
            $table->integer('age')->nullable();
            $table->string('salary_from')->nullable();
            $table->string('salary_to')->nullable();
            $table->string('type');
            $table->string('status');
            $table->date('start_date');
            $table->date('expire_date');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};

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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->string('phone');
            $table->string('document')->nullable();
            $table->date('from');
            $table->date('to')->nullable();
            $table->integer('days');
            $table->boolean('manager_approval')->default(false);
            $table->boolean('hod_approval')->default(false);
            $table->boolean('hr_approval')->default(false);
            $table->text('comment')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};

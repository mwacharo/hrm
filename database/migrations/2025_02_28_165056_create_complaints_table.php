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
        Schema::create('complaints', function (Blueprint $table) {
          

            $table->id();
            $table->string('subject');
            $table->text('description')->nullable();
            $table->boolean('is_anonymous')->default(true);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('category')->nullable();
            $table->string('status')->default('Open');
            $table->string('priority')->nullable();
            $table->date('date_opened')->nullable();
            $table->date('closed_date')->nullable();
            $table->text('comments')->nullable();
            $table->text('resolution')->nullable();
            $table->json('attachments')->nullable();
            $table->json('links')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('kin')->nullable();
            $table->string('kin_contact')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('country')->nullable();
            $table->string('nationality')->nullable();
            $table->string('region')->nullable();
            $table->string('spouse')->nullable();
            $table->string('spouse_no')->nullable();
            $table->string('mpesa_no')->nullable();
            $table->string('nhif_no')->nullable();
            $table->string('kra_pin')->nullable();
            $table->string('nssf_no')->nullable();
            $table->string('national_id')->unique()->nullable();
            $table->string('gender')->nullable();
            $table->string('staffID')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('user_details');
    }
};

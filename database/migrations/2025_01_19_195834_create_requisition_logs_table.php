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
        Schema::create('requisition_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained()->onDelete('cascade'); // Link to requisitions
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User performing the action
            $table->string('action'); // Action performed (e.g., "Created", "Updated", "Approved")
            $table->text('details')->nullable(); // Additional details, such as comments or changes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_logs');
    }
};

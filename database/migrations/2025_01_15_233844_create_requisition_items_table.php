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
        Schema::create('requisition_items', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('requisition_id')->constrained()->cascadeOnDelete(); // Foreign key to requisitions table

            $table->string('name'); 
            $table->text('description')->nullable(); 
            $table->integer('quantity')->nullable(); 
            $table->decimal('unit_cost', 15, 2)->nullable(); 
            $table->decimal('total_cost', 15, 2)->nullable(); 

            $table->timestamps(); 
            $table->softDeletes(); 
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_items');
    }
};

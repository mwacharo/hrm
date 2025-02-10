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
        Schema::table('leave_balances', function (Blueprint $table) {
            //

            $table->decimal('balance', 8, 2)->default(0)->change();
            $table->decimal('allocated', 8, 2)->default(0)->change();
            $table->decimal('taken', 8, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_balances', function (Blueprint $table) {
            //
            $table->integer('balance')->default(0)->change();
            $table->integer('allocated')->default(0)->change();
            $table->integer('taken')->default(0)->change();

            
        });
    }
};



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
        Schema::create('session_domework_business_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')
          ->constrained('sessions')
          ->onDelete('cascade');

            $table->foreignId('domework_id')
                ->constrained('domeworks')
                ->onDelete('cascade');

            $table->foreignId('businessplan_id')
                ->constrained('business_plans')
                ->onDelete('cascade');
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_domework_business_plans');
    }
};

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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_name');
            $table->text('session_objectives')->nullable;
            $table->string('session_duration')->nullable;
            $table->string('zoom_link')->nullable;
            $table->enum('status', ['1', '2'])->default('1'); // 1 = active, 2= cancelled
            $table->string('instructor_ids');
            $table->string('participant_ids');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};

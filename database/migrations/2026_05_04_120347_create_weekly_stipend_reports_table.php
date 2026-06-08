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
        Schema::create('weekly_stipend_reports', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');

            $table->date('week_start');
            $table->date('week_end');

            $table->integer('total_classes')->default(0);
            $table->integer('present_count')->default(0);

            $table->boolean('stipend_payment_status')->default(0);

            $table->decimal('attendance_percentage', 5, 2)->default(0);
            $table->decimal('stipend_amount', 10, 2)->default(0);
            $table->decimal('adjusted_stipend_amount', 10, 2)->default(0);
            $table->decimal('settled_stipend_amount', 10, 2)->default(0);
            $table->boolean('generation_status')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'week_start', 'week_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_stipend_reports');
    }
};

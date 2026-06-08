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
        Schema::table('users_classes_mappings', function (Blueprint $table) {

            // Step 1: Drop foreign key
            $table->dropForeign(['instructor_id']);

            // Step 2: Drop column
            $table->dropColumn('instructor_id');
        });

        Schema::table('users_classes_mappings', function (Blueprint $table) {

            // Step 3: Add as TEXT
            $table->text('instructor_id')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

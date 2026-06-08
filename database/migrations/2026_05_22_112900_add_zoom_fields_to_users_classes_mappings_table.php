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
            $table->string('zoom_join_url')->nullable()->after('session_id');
            $table->string('registrant_id')->nullable()->after('zoom_join_url');
            $table->string('instructor_host_key')->nullable()->after('registrant_id');
            $table->text('instructor_start_url')->nullable()->after('instructor_host_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_classes_mappings', function (Blueprint $table) {
            $table->dropColumn(['zoom_join_url', 'registrant_id', 'instructor_host_key', 'instructor_start_url']);
        });
    }
};

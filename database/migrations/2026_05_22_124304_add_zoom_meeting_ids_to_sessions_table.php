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
        Schema::table('sessions', function (Blueprint $table) {
            $table->string('zoom_link')->nullable()->change();
            $table->string('zoom_meeting_id')->nullable()->after('zoom_link');
            $table->string('zoom_meeting_password')->nullable()->after('zoom_meeting_id');
            $table->string('zoom_meeting_url')->nullable()->after('zoom_meeting_password');
            $table->string('zoom_registration_url')->nullable()->after('zoom_meeting_url');
            $table->text('zoom_start_url')->nullable()->after('zoom_registration_url');
            $table->json('zoom_instructor_host_keys')->nullable()->after('zoom_start_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn([
                'zoom_meeting_id',
                'zoom_meeting_password',
                'zoom_meeting_url',
                'zoom_registration_url',
                'zoom_start_url',
                'zoom_instructor_host_keys'
            ]);
        });
    }
};

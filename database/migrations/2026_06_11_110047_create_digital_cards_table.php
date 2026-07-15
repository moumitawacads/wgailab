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
        Schema::create('digital_cards', function (Blueprint $table) {
            $table->id();
            $table->string('dcard_id')->unique()->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->boolean('is_active')->default(true);

            $table->string('profile_image')->nullable();
            $table->string('brand_banner')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('job_title')->nullable();
            $table->string('company_name')->nullable();
            $table->string('address')->nullable();

            $table->json('contact_informations')->nullable();

            $table->json('promotional_content')->nullable();

            $table->json('testimonials')->nullable();

            $table->text('presskit')->nullable();

            $table->json('social_links')->nullable();

            $table->string('cxm_link')->nullable();
            $table->enum('theme_setting', ['dark', 'light'])->default('dark');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_cards');
    }
};

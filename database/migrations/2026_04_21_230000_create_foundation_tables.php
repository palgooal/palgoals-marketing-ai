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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('brand_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('brand_name');
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->text('tone_of_voice')->nullable();
            $table->string('primary_language')->default('ar');
            $table->string('secondary_language')->nullable()->default('en');
            $table->json('target_markets_json')->nullable();
            $table->json('usp_json')->nullable();
            $table->json('objections_json')->nullable();
            $table->json('cta_preferences_json')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('brand_profiles');
        Schema::dropIfExists('organizations');
    }
};

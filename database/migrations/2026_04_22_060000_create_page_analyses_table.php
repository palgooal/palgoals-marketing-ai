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
        Schema::create('page_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prompt_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('page_title')->nullable();
            $table->string('page_url')->nullable();
            $table->string('page_type')->nullable();
            $table->json('input_payload')->nullable();
            $table->longText('findings_text')->nullable();
            $table->longText('recommendations_text')->nullable();
            $table->integer('score')->nullable();
            $table->string('model_name')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_analyses');
    }
};

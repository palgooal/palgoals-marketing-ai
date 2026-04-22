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
        Schema::create('strategy_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prompt_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('period_type');
            $table->string('title')->nullable();
            $table->json('goals_json')->nullable();
            $table->json('input_payload')->nullable();
            $table->longText('output_text')->nullable();
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
        Schema::dropIfExists('strategy_plans');
    }
};

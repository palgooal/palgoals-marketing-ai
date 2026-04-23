<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_generations', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('status');
            $table->timestamp('published_at')->nullable()->after('is_published');
        });

        Schema::table('offer_generations', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('status');
            $table->timestamp('published_at')->nullable()->after('is_published');
        });

        Schema::table('strategy_plans', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('status');
            $table->timestamp('published_at')->nullable()->after('is_published');
        });

        Schema::table('page_analyses', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('status');
            $table->timestamp('published_at')->nullable()->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('content_generations', function (Blueprint $table) {
            $table->dropColumn(['is_published', 'published_at']);
        });

        Schema::table('offer_generations', function (Blueprint $table) {
            $table->dropColumn(['is_published', 'published_at']);
        });

        Schema::table('strategy_plans', function (Blueprint $table) {
            $table->dropColumn(['is_published', 'published_at']);
        });

        Schema::table('page_analyses', function (Blueprint $table) {
            $table->dropColumn(['is_published', 'published_at']);
        });
    }
};

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
        Schema::create('weekly_card_settings', function (Blueprint $table) {
        $table->id();
        $table->string('image_path')->nullable();
        $table->string('link_url', 500)->nullable();
        $table->string('title')->default('REGIONAL LMI WEEKLY');
        $table->string('subtitle')->default('WEEKLY TRENDS BULLETIN');
        $table->string('description')->default('Direct insights on weekly hiring trends and vacancy fluctuations in the Davao region. (Based on PhilJobNet)');
        $table->boolean('is_published')->default(false);
        $table->boolean('has_draft_changes')->default(false);
        $table->timestamp('published_at')->nullable();
        $table->json('published_snapshot')->nullable(); // ← snapshot of weekly_issues at publish time
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_card_settings');
    }
};

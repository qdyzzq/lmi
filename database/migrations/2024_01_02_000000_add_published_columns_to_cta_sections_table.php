<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cta_sections', function (Blueprint $table) {
            $table->string('published_title', 255)->nullable()->after('subtitle');
            $table->string('published_subtitle', 1000)->nullable()->after('published_title');
        });
    }

    public function down(): void
    {
        Schema::table('cta_sections', function (Blueprint $table) {
            $table->dropColumn(['published_title', 'published_subtitle']);
        });
    }
};

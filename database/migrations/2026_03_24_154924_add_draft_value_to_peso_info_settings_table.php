<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peso_info_settings', function (Blueprint $table) {
            $table->longText('draft_value')->nullable()->after('value');
            $table->timestamp('published_at')->nullable()->after('draft_value');
        });
    }

    public function down(): void
    {
        Schema::table('peso_info_settings', function (Blueprint $table) {
            $table->dropColumn(['draft_value', 'published_at']);
        });
    }
};
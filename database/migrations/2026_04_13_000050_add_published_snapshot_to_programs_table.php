<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
           
            $table->json('published_snapshot')->nullable()->after('is_published');

            $table->boolean('has_draft_changes')->default(false)->after('published_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['published_snapshot', 'has_draft_changes']);
        });
    }
};

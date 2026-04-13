<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            // Stores a frozen JSON snapshot of the program at the time of publishing.
            // The public page reads from this snapshot; live DB rows are always "draft".
            $table->json('published_snapshot')->nullable()->after('is_published');

            // Tracks whether live data has changed since the last publish.
            // Set to true on any edit, reset to false on publish.
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

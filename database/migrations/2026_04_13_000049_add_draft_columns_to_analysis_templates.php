<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analysis_templates', function (Blueprint $table) {
            // Stores the admin's pending edit text without touching template_text
            // (which must remain the live published content for the public view).
            // draft_submitted_at being non-null signals a pending edit exists.
            $table->text('draft_text')->nullable()->after('template_text');
            $table->string('draft_submitted_by')->nullable()->after('draft_text');
            $table->timestamp('draft_submitted_at')->nullable()->after('draft_submitted_by');
        });
    }

    public function down(): void
    {
        Schema::table('analysis_templates', function (Blueprint $table) {
            $table->dropColumn(['draft_text', 'draft_submitted_by', 'draft_submitted_at']);
        });
    }
};

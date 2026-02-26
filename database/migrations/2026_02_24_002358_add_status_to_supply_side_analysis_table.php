<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supply_side_analysis', function (Blueprint $table) {
            $table->enum('status', ['pending', 'published'])
                  ->default('published')  // existing records stay published
                  ->after('is_active');

            $table->string('submitted_by')->nullable()->after('status');
            $table->timestamp('submitted_at')->nullable()->after('submitted_by');
        });
    }

    public function down(): void
    {
        Schema::table('supply_side_analysis', function (Blueprint $table) {
            $table->dropColumn(['status', 'submitted_by', 'submitted_at']);
        });
    }
};
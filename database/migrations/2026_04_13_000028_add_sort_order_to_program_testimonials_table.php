<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ProgramTestimonial;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_testimonials', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('is_active');
        });

        // Set initial sort_order based on id per program
        ProgramTestimonial::query()
            ->orderBy('program_id')
            ->orderBy('id')
            ->each(function ($t, $index) {
                static $counters = [];
                $counters[$t->program_id] = ($counters[$t->program_id] ?? 0) + 1;
                $t->update(['sort_order' => $counters[$t->program_id]]);
            });
    }

    public function down(): void
    {
        Schema::table('program_testimonials', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publication_issues', function (Blueprint $table) {
            $table->id();
            $table->enum('group_id', ['lmp', 'jlmf', 'lmu']);
            $table->string('title');
            $table->text('description');
            $table->string('year', 20);         // supports '2024' and '2026-2027'
            $table->string('drive_file_id', 100);
            $table->integer('sort_order')->default(0);

            // Per-group publish/draft/snapshot is handled in the controller
            // by filtering snapshot per group_id — no extra columns needed here.
            $table->timestamps();

            $table->index(['group_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_issues');
    }
};
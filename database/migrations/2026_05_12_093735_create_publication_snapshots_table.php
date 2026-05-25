<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publication_snapshots', function (Blueprint $table) {
            $table->enum('group_id', ['lmp', 'jlmf', 'lmu'])->primary();
            $table->boolean('is_published')->default(false);
            $table->boolean('has_draft_changes')->default(true);
            $table->json('published_snapshot')->nullable();  
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_snapshots');
    }
};
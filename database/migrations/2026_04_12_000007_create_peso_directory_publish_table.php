<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peso_directory_publish', function (Blueprint $table) {
            $table->id();
            $table->json('published_snapshot')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('has_draft_changes')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peso_directory_publish');
    }
};
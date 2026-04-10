<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // -------------------------------------------------------
        // 1. peso_directory_publish  (singleton publish record)
        //    Stores the combined published snapshot + publish state
        //    for both the Field Offices directory and PESO Info.
        // -------------------------------------------------------
        Schema::create('peso_directory_publish', function (Blueprint $table) {
            $table->id();
            $table->json('published_snapshot')->nullable()
                  ->comment('Combined snapshot: field offices grouped by province + peso_info key');
            $table->timestamp('published_at')->nullable()
                  ->comment('NULL = never published');
            $table->boolean('has_draft_changes')->default(false)
                  ->comment('True when a change has been made since last publish');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peso_directory_publish');
    }
};

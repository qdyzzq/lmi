<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_how_to_apply', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->text('content');
            $table->string('link', 500)->nullable();
            $table->integer('sort_order');
            $table->timestamps();

            $table->foreign('program_id')
                  ->references('id')
                  ->on('programs')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_how_to_apply');
    }
};
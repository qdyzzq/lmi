<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_qualifications', function (Blueprint $table) {
            $table->string('type', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('program_qualifications', function (Blueprint $table) {
            $table->enum('type', ['qualification', 'requirement', 'beneficiary', 'service', 'objective'])->change();
        });
    }
};
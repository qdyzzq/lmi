<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lmi_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('respondent_name');
            $table->string('position');
            $table->string('contact_number', 20);
            $table->string('contact_type', 10)->default('mobile');
            $table->string('email');
            $table->string('industry_sector');
            $table->string('company_size');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lmi_submissions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lmi_submissions', function (Blueprint $table) {
            // Change contact_number from varchar(255) to varchar(20)
            // Keeps it as a string — phone numbers should NEVER be stored
            // as integers (leading zeros like 09xx would be lost with BigInt)
            $table->string('contact_number', 20)->change();

            // Add contact_type column after contact_number
            $table->string('contact_type', 10)->default('mobile')->after('contact_number');
            // Values: 'mobile' or 'telephone'
        });
    }

    public function down(): void
    {
        Schema::table('lmi_submissions', function (Blueprint $table) {
            $table->string('contact_number', 255)->change();
            $table->dropColumn('contact_type');
        });
    }
};
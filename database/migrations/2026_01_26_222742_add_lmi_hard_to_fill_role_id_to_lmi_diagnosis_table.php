<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add the column as nullable first (to allow existing rows)
        Schema::table('lmi_diagnosis', function (Blueprint $table) {
            $table->unsignedBigInteger('lmi_hard_to_fill_role_id')->nullable()->after('lmi_submission_id');
        });
        
        // Step 2: Delete orphaned diagnosis records that don't have a matching submission
        // This cleans up any bad data
        DB::statement('
            DELETE FROM lmi_diagnosis 
            WHERE lmi_submission_id NOT IN (SELECT id FROM lmi_submissions)
        ');
        
        // Step 3: For existing diagnosis records, try to link them to hard-to-fill roles
        // If a submission has roles, link the first diagnosis to the first role
        DB::statement('
            UPDATE lmi_diagnosis d
            INNER JOIN (
                SELECT 
                    lmi_submission_id,
                    MIN(id) as first_role_id
                FROM lmi_hard_to_fill_roles
                GROUP BY lmi_submission_id
            ) r ON d.lmi_submission_id = r.lmi_submission_id
            SET d.lmi_hard_to_fill_role_id = r.first_role_id
            WHERE d.lmi_hard_to_fill_role_id IS NULL
        ');
        
        // Step 4: Delete any diagnosis records that still don't have a role
        // (these are orphaned records with no matching hard-to-fill role)
        DB::statement('
            DELETE FROM lmi_diagnosis 
            WHERE lmi_hard_to_fill_role_id IS NULL
        ');
        
        // Step 5: Now make the column NOT NULL and add the foreign key
        Schema::table('lmi_diagnosis', function (Blueprint $table) {
            $table->unsignedBigInteger('lmi_hard_to_fill_role_id')->nullable(false)->change();
            
            $table->foreign('lmi_hard_to_fill_role_id')
                  ->references('id')
                  ->on('lmi_hard_to_fill_roles')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('lmi_diagnosis', function (Blueprint $table) {
            $table->dropForeign(['lmi_hard_to_fill_role_id']);
            $table->dropColumn('lmi_hard_to_fill_role_id');
        });
    }
};
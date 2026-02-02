<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lmi_hard_to_fill_roles', function (Blueprint $table) {
            $table->string('job_title_normalized')->nullable()->index()->after('job_title');
        });
        
        // Normalize existing data
        DB::table('lmi_hard_to_fill_roles')->get()->each(function ($role) {
            $normalized = strtolower(trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9\s]/i', '', $role->job_title))));
            
            DB::table('lmi_hard_to_fill_roles')
                ->where('id', $role->id)
                ->update(['job_title_normalized' => $normalized]);
        });
    }

    public function down()
    {
        Schema::table('lmi_hard_to_fill_roles', function (Blueprint $table) {
            $table->dropColumn('job_title_normalized');
        });
    }
};
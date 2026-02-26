<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    DB::statement("ALTER TABLE discipline_enrollments 
        MODIFY COLUMN institution_type ENUM('Private', 'Public', 'Total') NOT NULL");
}

public function down()
{
    DB::statement("ALTER TABLE discipline_enrollments 
        MODIFY COLUMN institution_type ENUM('Private', 'Public') NOT NULL");
}
};

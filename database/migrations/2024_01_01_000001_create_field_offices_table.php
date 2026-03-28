<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_offices', function (Blueprint $table) {
            $table->id();
            $table->string('province');
            $table->string('name');
            $table->string('office_type');          // PESO, JPO, DOLE, TESDA, etc. — open-ended string
            $table->foreignId('position_title_id')  // FK → position_titles.id
                  ->nullable()
                  ->constrained('position_titles')
                  ->nullOnDelete();
            $table->string('persons_name')->nullable(); // the actual person holding the position
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('province');
            $table->index('office_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_offices');
    }
};
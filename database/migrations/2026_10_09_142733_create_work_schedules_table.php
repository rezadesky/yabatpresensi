<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->onDelete('cascade');
            $table->string('name'); // Reguler STKIP, Guru SMK, Shif Asrama Thawalib, dll.
            $table->string('day_of_week')->nullable(); // Senin, Selasa, ... atau ALL / Weekday
            $table->time('time_in'); // 07:45:00
            $table->time('time_out'); // 16:00:00
            $table->integer('late_tolerance_minutes')->default(15);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_schedules');
    }
};

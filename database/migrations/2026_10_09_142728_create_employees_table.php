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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('institution_id')->constrained('institutions')->onDelete('cascade');
            $table->string('nip_nidn')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('gender')->nullable(); // L / P
            $table->string('position'); // Dosen, Guru, Ustadz, Tenaga Kependidikan, Admin Unit
            $table->enum('employment_status', ['tetap', 'kontrak', 'honorer'])->default('tetap');
            $table->boolean('is_active')->default(true);
            $table->text('address')->nullable();
            $table->date('join_date')->nullable();
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
        Schema::dropIfExists('employees');
    }
};

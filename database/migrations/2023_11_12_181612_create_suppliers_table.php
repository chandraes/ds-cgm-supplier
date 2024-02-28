<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nickname');
            $table->string('no_wa');
            $table->text('alamat');
            $table->string('npwp');
            $table->string('no_ktp');
            $table->string('bank');
            $table->string('no_rek');
            $table->string('nama_rek');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};

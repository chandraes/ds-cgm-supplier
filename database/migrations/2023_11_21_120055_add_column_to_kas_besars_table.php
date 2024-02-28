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
        Schema::table('kas_besars', function (Blueprint $table) {
            $table->string('kode_titipan')->after('nomor_deposit')->default('T');
            $table->bigInteger('nomor_titipan')->after('kode_titipan')->nullable();
        });

     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kas_besars', function (Blueprint $table) {
            $table->dropColumn('kode_titipan');
            $table->dropColumn('nomor_titipan');
        });
    }
};

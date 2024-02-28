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
            $table->string('kode_deposit')->default('D')->after('uraian');
            $table->integer('nomor_deposit')->nullable()->after('kode_deposit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kas_besars', function (Blueprint $table) {
            $table->dropColumn('kode_deposit');
            $table->dropColumn('nomor_deposit');
        });
    }
};

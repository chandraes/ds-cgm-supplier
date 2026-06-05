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
        Schema::table('laba_simpans', function (Blueprint $table) {
            $table->string('nama_rek')->nullable()->after('uraian');
            $table->string('bank')->nullable()->after('nama_rek');
            $table->string('no_rek')->nullable()->after('bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laba_simpans', function (Blueprint $table) {
            $table->dropColumn(['nama_rek', 'bank', 'no_rek']);
        });
    }
};

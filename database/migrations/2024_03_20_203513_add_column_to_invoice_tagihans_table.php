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
        Schema::table('invoice_tagihans', function (Blueprint $table) {
            $table->bigInteger('nilai_ppn')->after('nilai_tagihan')->default(0);
            $table->bigInteger('nilai_pph')->after('nilai_ppn')->default(0);
            $table->boolean('ppn')->after('estimasi_pembayaran')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_tagihans', function (Blueprint $table) {
            $table->dropColumn('nilai_ppn');
            $table->dropColumn('nilai_pph');
            $table->dropColumn('ppn');
        });
    }
};

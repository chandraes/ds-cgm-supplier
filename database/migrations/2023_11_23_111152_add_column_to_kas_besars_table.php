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
            $table->foreignId('invoice_tagihan_id')->nullable()->after('modal_investor_terakhir')->constrained('invoice_tagihans');
            $table->string('kode_tagihan')->default('TC')->after('nomor_titipan');
            $table->integer('nomor_tagihan')->nullable()->after('kode_tagihan');
            $table->string('kode_bayar')->default('BS')->after('nomor_tagihan');
            $table->integer('nomor_bayar')->nullable()->after('kode_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kas_besars', function (Blueprint $table) {
            $table->dropForeign(['invoice_tagihan_id']);
            $table->dropColumn('invoice_tagihan_id');
            $table->dropColumn('kode_tagihan');
            $table->dropColumn('nomor_tagihan');
            $table->dropColumn('kode_bayar');
            $table->dropColumn('nomor_bayar');
        });
    }
};

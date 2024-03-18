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
            $table->boolean('cutoff')->default(0)->after('dibayar');
            $table->date('estimasi_pembayaran')->nullable()->after('cutoff');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_tagihans', function (Blueprint $table) {
            $table->dropColumn('cutoff');
            $table->dropColumn('estimasi_pembayaran');
        });
    }
};

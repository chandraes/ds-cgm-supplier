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
        Schema::table('transaksis', function (Blueprint $table) {
            // drop unique from nota_timbangan if exists

            $table->dropUnique('transaksis_nota_timbangan_unique');
            // make customer_id + nota_timbangan unique
            $table->unique(['customer_id', 'nota_timbangan'], 'nota_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // drop unique from customer_id + nota_timbangan
            $table->dropUnique('nota_unique');

        });
    }
};

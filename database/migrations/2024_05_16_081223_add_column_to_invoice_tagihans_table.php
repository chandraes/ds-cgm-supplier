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
            $table->boolean('pph_badan')->default(1)->after('ppn')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_tagihans', function (Blueprint $table) {
            $table->dropColumn('pph_badan');
        });
    }
};

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
        Schema::create('laba_simpans', function (Blueprint $table) {
            $table->id();
            $table->string('uraian')->nullable();
            $table->bigInteger('nominal');
            $table->bigInteger('saldo');
            $table->enum('jenis', ['in', 'out']);
            $table->foreignId('invoice_tagihan_id')->nullable()->constrained('invoice_tagihans')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laba_simpans');
    }
};

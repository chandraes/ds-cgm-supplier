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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(false);
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('project_id')->constrained('projects');
            $table->index('status');
            $table->date('tanggal');
            $table->string('nota_timbangan')->unique();
            $table->integer('berat');
            $table->integer('harga');
            $table->integer('total');
            $table->integer('pph');
            $table->integer('profit');
            $table->boolean('tagihan')->default(false);
            $table->boolean('bayar')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};

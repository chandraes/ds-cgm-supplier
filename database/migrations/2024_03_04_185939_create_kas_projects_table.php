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
        Schema::create('kas_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->string('uraian');
            $table->boolean('jenis_transaksi');
            $table->bigInteger('nominal');
            $table->bigInteger('saldo');
            $table->bigInteger('modal_investor');
            $table->bigInteger('modal_investor_terakhir');
            $table->bigInteger('total_tagihan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_projects');
    }
};

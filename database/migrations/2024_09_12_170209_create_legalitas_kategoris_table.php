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
        Schema::create('legalitas_kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        $data = [
            'AKTE PERUSAHAAN & SK',
            'NIB',
            'NPWP',
            'SPPKP',
            'DOMISILI',
            'IJIN USAHA',
            'SERTIFIKAT',
            'BIODATA PEMEGANG SAHAM'
        ];

        foreach ($data as $item) {
            \App\Models\Legalitas\LegalitasKategori::create([
                'nama' => $item
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legalitas_kategoris');
    }
};

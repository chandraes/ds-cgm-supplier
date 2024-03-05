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
        Schema::table('kas_projects', function (Blueprint $table) {
            $table->dropColumn('total_tagihan');
            $table->bigInteger('saldo_project')->after('saldo');
            $table->bigInteger('modal_investor_project')->after('modal_investor_terakhir');
            $table->bigInteger('modal_investor_project_terakhir')->after('modal_investor_project');
            $table->string('no_rek')->after('modal_investor_project_terakhir');
            $table->string('nama_rek')->after('no_rek');
            $table->string('bank')->after('nama_rek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kas_projects', function (Blueprint $table) {
            $table->bigInteger('total_tagihan')->after('modal_investor_terakhir');
            $table->dropColumn('modal_investor_project');
            $table->dropColumn('modal_investor_project_terakhir');
            $table->dropColumn('saldo_project');
            $table->dropColumn('no_rek');
            $table->dropColumn('nama_rek');
            $table->dropColumn('bank');
        });
    }
};

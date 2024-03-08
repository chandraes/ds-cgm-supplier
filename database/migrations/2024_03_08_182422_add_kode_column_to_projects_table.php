<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('kode')->nullable()->after('id');
        });

        // create kode for existing projects with max + 1
        $projects = DB::table('projects')->get();
        foreach ($projects as $key => $project) {
            $kode = $key + 1;
            DB::table('projects')->where('id', $project->id)->update(['kode' => $kode]);
        }

        // change kode to not nullable
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('kode')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('kode');
        });
    }
};

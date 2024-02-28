<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama_status' => 'On Progress'],
            ['nama_status' => 'Dibatalkan'],
            ['nama_status' => 'Selesai'],
            ['nama_status' => 'Ditunda'],
        ];

        \App\Models\ProjectStatus::insert($data);
    }
}

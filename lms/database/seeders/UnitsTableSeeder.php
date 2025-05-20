<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('units')->insert([
            ['unit_name' => 'Fizik Bilimi ve Kariyer Keşfi', 'class_level' => '9'],
            ['unit_name' => 'Kuvvet ve Hareket', 'class_level' => '9'],
            ['unit_name' => 'Akışkanlar', 'class_level' => '9'],
            ]);
    }
}

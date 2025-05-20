<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopicsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('topics')->insert([
            // 9. Sınıf Üniteleri
            ['topic_name' => 'Fizik Bilimi', 'unit_id' => 1], // Fizik Bilimine Giriş
            ['topic_name' => 'Fizik Biliminın Alt Dalları', 'unit_id' => 1], // Fizik Bilimine Giriş
            ['topic_name' => 'Fizik Bilimine Yön Verenler', 'unit_id' => 1], // Madde ve Özellikleri
            ['topic_name' => 'Fizik Bilimi İle Kariyer Keşfi', 'unit_id' => 1], // Madde ve Özellikleri
            ['topic_name' => 'Temel ve Türetilmiş Nicelikler', 'unit_id' => 2], // Hareket ve Kuvvet
            ['topic_name' => 'Skaler ve Vektörel Nicelikler', 'unit_id' => 2], // Hareket ve Kuvvet
            ['topic_name' => 'Vektöreller', 'unit_id' => 2],
            ['topic_name' => 'Doğadaki Temel Kuvvetler', 'unit_id' => 2],
            ['topic_name' => 'Hareket ve Hareket Türleri', 'unit_id' => 2],
            ['topic_name' => 'Basınç', 'unit_id' => 3],
            ['topic_name' => 'Sıvılarda Basınç', 'unit_id' => 3],
            ['topic_name' => 'Açık Hava Basıncı', 'unit_id' =>3],
            ['topic_name' => 'Kaldırma Kuvveti', 'unit_id' => 3],
            ['topic_name' => 'Bernoulli İlkesi', 'unit_id' => 3],

             // Kuantum Fiziği
        ]);
    }
}

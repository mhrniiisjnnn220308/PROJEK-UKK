<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Table;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            ['nomor_meja' => '1', 'kapasitas' => 4, 'status' => 'tersedia', 'keterangan' => 'Meja dekat jendela'],
            ['nomor_meja' => '2', 'kapasitas' => 4, 'status' => 'tersedia', 'keterangan' => 'Meja tengah'],
            ['nomor_meja' => '3', 'kapasitas' => 2, 'status' => 'tersedia', 'keterangan' => 'Meja kecil'],
            ['nomor_meja' => '4', 'kapasitas' => 6, 'status' => 'tersedia', 'keterangan' => 'Meja besar'],
            ['nomor_meja' => '5', 'kapasitas' => 4, 'status' => 'tersedia', 'keterangan' => 'Meja sudut'],
            ['nomor_meja' => '6', 'kapasitas' => 4, 'status' => 'tersedia', 'keterangan' => 'Meja outdoor'],
            ['nomor_meja' => '7', 'kapasitas' => 8, 'status' => 'tersedia', 'keterangan' => 'Meja VIP'],
            ['nomor_meja' => '8', 'kapasitas' => 2, 'status' => 'tersedia', 'keterangan' => 'Meja couple'],
        ];

        foreach ($tables as $table) {
            Table::create($table);
        }
    }
}
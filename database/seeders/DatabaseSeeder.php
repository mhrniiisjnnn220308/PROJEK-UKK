<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Insert Users
        DB::table('users')->insert([
            [
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'nama' => 'Administrator',
                'role' => 'admin',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kasir1',
                'password' => Hash::make('kasir123'),
                'nama' => 'Kasir Satu',
                'role' => 'kasir',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'owner',
                'password' => Hash::make('owner123'),
                'nama' => 'Owner Foodesia',
                'role' => 'owner',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert Categories
        DB::table('categories')->insert([
            ['nama_kategori' => 'Makanan', 'deskripsi' => 'Kategori untuk makanan', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Minuman', 'deskripsi' => 'Kategori untuk minuman', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Snack', 'deskripsi' => 'Kategori untuk snack', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert Products
        DB::table('products')->insert([
            ['kategori_id' => 1, 'nama_produk' => 'Nasi Goreng', 'foto' => null, 'deskripsi' => 'Nasi goreng spesial dengan telur', 'harga_produk' => 15000, 'stok' => 50, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_id' => 1, 'nama_produk' => 'Mie Goreng', 'foto' => null, 'deskripsi' => 'Mie goreng pedas dengan sayuran', 'harga_produk' => 15000, 'stok' => 50, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_id' => 1, 'nama_produk' => 'Ayam Goreng', 'foto' => null, 'deskripsi' => 'Ayam goreng kriuk dengan sambal', 'harga_produk' => 20000, 'stok' => 30, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_id' => 2, 'nama_produk' => 'Es Teh Manis', 'foto' => null, 'deskripsi' => 'Es teh manis segar', 'harga_produk' => 5000, 'stok' => 100, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_id' => 2, 'nama_produk' => 'Es Jeruk', 'foto' => null, 'deskripsi' => 'Es jeruk peras segar', 'harga_produk' => 7000, 'stok' => 100, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_id' => 1, 'nama_produk' => 'Soto Ayam', 'foto' => null, 'deskripsi' => 'Soto ayam kuah kuning', 'harga_produk' => 18000, 'stok' => 40, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_id' => 1, 'nama_produk' => 'Gado-Gado', 'foto' => null, 'deskripsi' => 'Gado-gado dengan bumbu kacang', 'harga_produk' => 12000, 'stok' => 35, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_id' => 2, 'nama_produk' => 'Jus Alpukat', 'foto' => null, 'deskripsi' => 'Jus alpukat segar dengan coklat', 'harga_produk' => 10000, 'stok' => 50, 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert Logs
        DB::table('logs')->insert([
            ['id_user' => 1, 'activity' => 'Admin login ke sistem', 'created_at' => now(), 'updated_at' => now()],
            ['id_user' => 1, 'activity' => 'Admin menambahkan produk: Nasi Goreng', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
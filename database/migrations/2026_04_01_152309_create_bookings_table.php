<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_meja');
            $table->string('nama_pelanggan', 100);
            $table->string('no_hp', 20);
            $table->date('tanggal_booking');
            $table->time('jam_kedatangan');
            $table->integer('jumlah_dp')->default(0);
            $table->text('catatan_pesanan')->nullable();
            $table->enum('status', ['pending', 'konfirmasi', 'selesai', 'batal'])->default('pending');
            $table->timestamps();
            
            $table->foreign('id_meja')->references('id')->on('tables')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
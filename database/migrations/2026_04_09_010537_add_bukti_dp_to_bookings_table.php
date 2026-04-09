<?php
// php artisan make:migration add_bukti_dp_to_bookings_table --table=bookings

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Cek dulu kolom mana yang belum ada di tabel kamu
            if (!Schema::hasColumn('bookings', 'bukti_dp')) {
                $table->string('bukti_dp')->nullable()->after('jumlah_dp');
            }
            if (!Schema::hasColumn('bookings', 'dp_verified')) {
                $table->boolean('dp_verified')->default(false)->after('bukti_dp');
            }
            if (!Schema::hasColumn('bookings', 'catatan_pesanan')) {
                $table->text('catatan_pesanan')->nullable()->after('jam_kedatangan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['bukti_dp', 'dp_verified', 'catatan_pesanan']);
        });
    }
};
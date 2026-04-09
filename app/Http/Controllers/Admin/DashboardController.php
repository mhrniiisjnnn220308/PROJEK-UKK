<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Log;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ── Statistik umum
        $totalProduk      = Product::where('status', 'aktif')->count();
        $totalUser        = User::where('status', 'aktif')->count();
        $produkStokRendah = Product::where('status', 'aktif')->where('stok', '<', 10)->count();

        $totalTransaksi = Transaction::whereNotNull('nomor_unik')
                            ->distinct('nomor_unik')
                            ->count('nomor_unik');

        // ── Statistik transaksi 
        $totalPendapatan = Transaction::sum('total_harga');

        $totalDineIn = Transaction::where('jenis_pemesanan', 'dine_in')
                        ->whereNotNull('nomor_unik')
                        ->distinct('nomor_unik')
                        ->count('nomor_unik');

        $totalTakeAway = Transaction::where('jenis_pemesanan', 'take_away')
                          ->whereNotNull('nomor_unik')
                          ->distinct('nomor_unik')
                          ->count('nomor_unik');

        // ── Pagination Log Aktivitas (Admin only) 
        $logPerPage     = 10;
        $logPage        = max(1, (int) $request->get('log_page', 1));

        $totalLogs      = Log::whereHas('user', fn($q) => $q->where('role', 'admin'))->count();
        $totalLogPages  = max(1, (int) ceil($totalLogs / $logPerPage));
        $logPage        = min($logPage, $totalLogPages);

        $logs = Log::with('user')
                   ->whereHas('user', fn($q) => $q->where('role', 'admin'))
                   ->orderBy('created_at', 'desc')
                   ->skip(($logPage - 1) * $logPerPage)
                   ->take($logPerPage)
                   ->get();

        
        $trxPerPage     = 10;
        $trxPage        = max(1, (int) $request->get('trx_page', 1));

        $semuaNomorUnik = Transaction::whereNotNull('nomor_unik')
                            ->selectRaw('nomor_unik, MAX(created_at) as latest')
                            ->groupBy('nomor_unik')
                            ->orderByDesc('latest')
                            ->pluck('nomor_unik');

        $totalUnikTransaksi = $semuaNomorUnik->count();
        $totalTrxPages      = max(1, (int) ceil($totalUnikTransaksi / $trxPerPage));
        $trxPage            = min($trxPage, $totalTrxPages);

        $nomorUnikPage = $semuaNomorUnik->slice(($trxPage - 1) * $trxPerPage, $trxPerPage)->values();

        $semuaTransaksi = Transaction::with(['product', 'table'])
                            ->whereIn('nomor_unik', $nomorUnikPage)
                            ->orderBy('created_at', 'desc')
                            ->get()
                            ->groupBy('nomor_unik')
                            ->sortKeysUsing(function ($a, $b) use ($nomorUnikPage) {
                                return $nomorUnikPage->search($a) <=> $nomorUnikPage->search($b);
                            });

        return view('admin.dashboard', compact(
            'totalProduk',
            'totalUser',
            'totalTransaksi',
            'produkStokRendah',
            'totalPendapatan',
            'totalDineIn',
            'totalTakeAway',
            // log
            'logs',
            'logPage',
            'totalLogPages',
            'logPerPage',
            'totalLogs',
            // transaksi
            'semuaTransaksi',
            'trxPage',
            'totalTrxPages',
            'trxPerPage',
            'totalUnikTransaksi'
        ));
    }
}
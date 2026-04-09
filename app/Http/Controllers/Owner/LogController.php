<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with('user');

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('user')) {
            $query->where('id_user', $request->user);
        }

        if ($request->filled('role')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(30);

        $userList = User::whereIn('role', ['admin', 'kasir'])
                        ->where('status', 'aktif')
                        ->orderBy('nama')
                        ->get();

        $totalAdmin   = Log::whereHas('user', fn($q) => $q->where('role', 'admin'))->count();
        $totalKasir   = Log::whereHas('user', fn($q) => $q->where('role', 'kasir'))->count();
        $totalHariIni = Log::whereDate('created_at', today())->count();

        return view('owner.logs.index', compact(
            'logs',
            'userList',
            'totalAdmin',
            'totalKasir',
            'totalHariIni'
        ));
    }

    public function printPdf(Request $request)
    {
        $query = Log::with('user');

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('user')) {
            $query->where('id_user', $request->user);
        }

        if ($request->filled('role')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $totalAdmin   = $logs->filter(fn($l) => $l->user->role === 'admin')->count();
        $totalKasir   = $logs->filter(fn($l) => $l->user->role === 'kasir')->count();
        $totalHariIni = Log::whereDate('created_at', today())->count();

        $filters = [
            'tanggal_mulai'   => $request->tanggal_mulai  ?? null,
            'tanggal_selesai' => $request->tanggal_selesai ?? null,
            'role'            => $request->role ? ucfirst($request->role) : 'Semua Role',
            'user'            => $request->user ? User::find($request->user)?->nama : 'Semua User',
        ];

        $pdf = Pdf::loadView('owner.logs.pdf', compact(
            'logs',
            'totalAdmin',
            'totalKasir',
            'totalHariIni',
            'filters'
        ))->setPaper('a4', 'landscape');

        $filename = 'Log_Aktivitas_' . date('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }
}
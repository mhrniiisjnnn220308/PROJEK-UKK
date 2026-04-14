<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class KasirLogController extends Controller
{
    public function index()
    {
        $logs = Log::with('user')
            ->where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('kasir.logs.index', compact('logs'));
    }
}
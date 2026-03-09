<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\User;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with('user');
        
        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }
        
        // Filter berdasarkan user
        if ($request->filled('user')) {
            $query->where('id_user', $request->user);
        }
        
        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('role', $request->role);
            });
        }
        
        $logs = $query->orderBy('created_at', 'desc')->paginate(30);
        
        // Data untuk filter
        $userList = User::whereIn('role', ['admin', 'kasir'])
                       ->where('status', 'aktif')
                       ->orderBy('nama')
                       ->get();
        
        return view('owner.logs.index', compact('logs', 'userList'));
    }
}
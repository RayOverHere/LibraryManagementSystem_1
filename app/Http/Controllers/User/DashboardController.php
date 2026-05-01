<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Auto-mark overdue
        auth()->user()->transactions()
            ->where('status', 'borrowed')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        $activeBorrows = auth()->user()->transactions()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->with('book')
            ->get();

        $history = auth()->user()->transactions()
            ->whereIn('status', ['returned', 'lost'])
            ->with('book')
            ->latest()
            ->get();

        return view('user.dashboard', compact('activeBorrows', 'history'));
    }
}

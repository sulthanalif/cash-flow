<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $incomeMonth = Transaction::whereYear('date', date('Y'))
            ->whereMonth('date', date('m'))
            ->whereHas('category', function ($query) {
                $query->where('type', 'income');
            })
            ->sum('amount');

        $expenseMonth = Transaction::whereYear('date', date('Y'))
            ->whereMonth('date', date('m'))
            ->whereHas('category', function ($query) {
                $query->where('type', 'expense');
            })
            ->sum('amount');

        $wallets = auth()->user()->wallets;
        $recentTransactions = Transaction::where('user_id', auth()->user()->id)
            ->with(['category', 'wallet'])
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'incomeMonth' => $incomeMonth,
            'expenseMonth' => $expenseMonth,
            'wallets' => $wallets,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}

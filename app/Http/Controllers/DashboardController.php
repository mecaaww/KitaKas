<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $theme = Auth::user()->theme ?? 'blue';

        // --- SALDO BERSAMA (semua user, wallet shared) ---
        $totalShared = Transaction::where('wallet_type', 'shared')
            ->selectRaw("
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) -
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as balance
            ")
            ->value('balance') ?? 0;

        // --- PENGELUARAN PRIBADI BULAN INI (user ini saja) ---
        $personalExpenseMonth = Transaction::where('user_id', $userId)
            ->where('wallet_type', 'personal')
            ->where('type', 'expense')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        // --- RIWAYAT PRIBADI (10 terakhir) ---
        $personalTransactions = Transaction::where('user_id', $userId)
            ->where('wallet_type', 'personal')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // --- RIWAYAT BERSAMA (10 terakhir, semua user) ---
        $sharedTransactions = Transaction::where('wallet_type', 'shared')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // --- GRAFIK 7 HARI TERAKHIR (pengeluaran user ini, semua wallet) ---
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $chartLabels[] = $day->translatedFormat('D, d M');

            $total = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereDate('date', $day->toDateString())
                ->sum('amount');

            $chartData[] = $total;
        }

        return view('dashboard', compact(
            'totalShared',
            'personalExpenseMonth',
            'personalTransactions',
            'sharedTransactions',
            'chartLabels',
            'chartData'
        ));
    }
}

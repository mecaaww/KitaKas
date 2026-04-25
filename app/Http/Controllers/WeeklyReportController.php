<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WeeklyReportController extends Controller
{
    public function index(Request $request)
    {
        $me     = Auth::user();
        $userId = $me->id;

        // Tentukan minggu yang ditampilkan
        // Default: minggu berjalan (Senin s.d. Minggu)
        $weekStartInput = $request->query('week');
        if ($weekStartInput) {
            $weekStart = Carbon::parse($weekStartInput)->startOfWeek(Carbon::MONDAY);
        } else {
            $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
        }
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Navigasi minggu
        $prevWeekStart = $weekStart->copy()->subWeek();
        $nextWeekStart = $weekStart->copy()->addWeek();

        // Cari pasangan
        $partnerGender = $me->gender === 'male' ? 'female' : 'male';
        $partner = User::where('gender', $partnerGender)->first();

        // ── PEMASUKAN & PENGELUARAN SAYA (personal + porsi shared) ──
        $myTransactions = Transaction::where('user_id', $userId)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get();

        $myIncome  = $myTransactions->where('type', 'income')->sum('amount');
        $myExpense = $myTransactions->where('type', 'expense')->sum('amount');

        // ── PEMASUKAN & PENGELUARAN PASANGAN (personal saja) ──
        $partnerIncome  = 0;
        $partnerExpense = 0;
        if ($partner) {
            $partnerTransactions = Transaction::where('user_id', $partner->id)
                ->where('wallet_type', 'personal')
                ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->get();

            $partnerIncome  = $partnerTransactions->where('type', 'income')->sum('amount');
            $partnerExpense = $partnerTransactions->where('type', 'expense')->sum('amount');
        }

        // ── GRAFIK HARIAN (7 hari dalam minggu ini) ──
        $chartLabels      = [];
        $weeklyIncome     = [];
        $weeklyExpense    = [];
        $myDailyExpense   = [];
        $partnerDailyExpense = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $chartLabels[] = $day->translatedFormat('D d/m');

            // Gabungan pemasukan & pengeluaran hari itu (saya)
            $dayMyTrx = $myTransactions->filter(fn($t) => $t->date->isSameDay($day));

            $incomeDay  = $dayMyTrx->where('type', 'income')->sum('amount');
            // Tambah pemasukan shared juga untuk grafik harian
            $incomeShared = Transaction::where('wallet_type', 'shared')
                ->where('type', 'income')
                ->whereDate('date', $day)
                ->sum('amount');

            $weeklyIncome[]  = $incomeDay + $incomeShared;
            $weeklyExpense[] = $dayMyTrx->where('type', 'expense')->sum('amount');

            $myDailyExpense[] = $dayMyTrx->where('type', 'expense')->sum('amount');

            if ($partner) {
                $partnerDailyExpense[] = Transaction::where('user_id', $partner->id)
                    ->where('wallet_type', 'personal')
                    ->where('type', 'expense')
                    ->whereDate('date', $day)
                    ->sum('amount');
            }
        }

        // ── SEMUA TRANSAKSI MINGGU INI (untuk tabel riwayat) ──
        $weekTransactions = Transaction::with('user')
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhere('wallet_type', 'shared');
            })
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('report.weekly', compact(
            'weekStart', 'weekEnd', 'prevWeekStart', 'nextWeekStart',
            'partner',
            'myIncome', 'myExpense',
            'partnerIncome', 'partnerExpense',
            'chartLabels', 'weeklyIncome', 'weeklyExpense',
            'myDailyExpense', 'partnerDailyExpense',
            'weekTransactions'
        ));
    }
}

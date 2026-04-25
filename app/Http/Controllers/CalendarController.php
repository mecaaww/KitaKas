<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Ambil bulan & tahun dari query param, default: bulan ini
        $month = $request->query('month', Carbon::now()->month);
        $year  = $request->query('year',  Carbon::now()->year);

        $currentMonth = Carbon::createFromDate($year, $month, 1);
        $prevMonth    = $currentMonth->copy()->subMonth();
        $nextMonth    = $currentMonth->copy()->addMonth();

        // Hari pertama jatuh di kolom ke-berapa (0=Min, 1=Sen, ..., 6=Sab)
        $firstDayOfWeek = $currentMonth->dayOfWeek; // 0=Sunday

        // Buat array semua hari dalam bulan ini
        $daysInMonth  = $currentMonth->daysInMonth;
        $calendarDays = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $calendarDays[] = Carbon::createFromDate($year, $month, $d);
        }

        // Ambil SEMUA transaksi bulan ini (pribadi user + bersama)
        $allTransactions = Transaction::where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhere('wallet_type', 'shared');
            })
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        // Kelompokkan per tanggal untuk kalender (aggregate: income & expense)
        $dailyData = [];
        foreach ($allTransactions as $trx) {
            $key = $trx->date->format('Y-m-d');
            if (!isset($dailyData[$key])) {
                $dailyData[$key] = ['income' => 0, 'expense' => 0];
            }
            if ($trx->type === 'income') {
                $dailyData[$key]['income'] += $trx->amount;
            } else {
                $dailyData[$key]['expense'] += $trx->amount;
            }
        }

        // Data lengkap transaksi per hari untuk modal detail (sebagai array JSON)
        $dailyTransactions = [];
        foreach ($allTransactions as $trx) {
            $key = $trx->date->format('Y-m-d');
            $dailyTransactions[$key][] = [
                'description' => $trx->description,
                'amount'      => $trx->amount,
                'type'        => $trx->type,
                'wallet_type' => $trx->wallet_type,
            ];
        }

        // Ringkasan bulan ini (hanya transaksi user sendiri + bersama)
        $monthlyIncome  = $allTransactions->where('type', 'income')->sum('amount');
        $monthlyExpense = $allTransactions->where('type', 'expense')->sum('amount');
        $totalTransactions = $allTransactions->count();

        // Tagihan (expense) yang akan jatuh tempo bulan ini (masih di masa depan)
        // Kita mark hari-hari yang punya pengeluaran sebagai "jatuh tempo" untuk notifikasi
        $billDueDays = [];
        foreach ($allTransactions->where('type', 'expense') as $trx) {
            if ($trx->date->isFuture()) {
                $billDueDays[$trx->date->format('Y-m-d')] = true;
            }
        }

        // Upcoming events (semua transaksi yang tanggalnya >= hari ini di bulan ini)
        $upcomingEvents = $allTransactions
            ->filter(fn($t) => $t->date->gte(Carbon::today()))
            ->sortBy('date')
            ->take(5);

        return view('calendar.index', compact(
            'currentMonth', 'prevMonth', 'nextMonth',
            'firstDayOfWeek', 'calendarDays',
            'dailyData', 'dailyTransactions',
            'monthlyIncome', 'monthlyExpense', 'totalTransactions',
            'billDueDays', 'upcomingEvents'
        ));
    }
}

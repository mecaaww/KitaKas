<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PartnerController extends Controller
{
    public function view()
    {
        $me = Auth::user();

        // Cari pasangan berdasarkan gender berlawanan
        $partnerGender = $me->gender === 'male' ? 'female' : 'male';
        $partner = User::where('gender', $partnerGender)->firstOrFail();

        $month = Carbon::now()->month;
        $year  = Carbon::now()->year;

        // ── SALDO BERSIH PRIBADI PASANGAN (semua waktu) ──
        $partnerPersonalIncomeAll  = Transaction::where('user_id', $partner->id)->where('wallet_type', 'personal')->where('type', 'income')->sum('amount');
        $partnerPersonalExpenseAll = Transaction::where('user_id', $partner->id)->where('wallet_type', 'personal')->where('type', 'expense')->sum('amount');
        $partnerPersonalBalance    = $partnerPersonalIncomeAll - $partnerPersonalExpenseAll;

        // ── PEMASUKAN & PENGELUARAN PRIBADI BULAN INI ──
        $partnerPersonalIncome       = Transaction::where('user_id', $partner->id)->where('wallet_type', 'personal')->where('type', 'income')->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
        $partnerPersonalExpenseMonth = Transaction::where('user_id', $partner->id)->where('wallet_type', 'personal')->where('type', 'expense')->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');

        // ── RIWAYAT PRIBADI (20 terakhir) ──
        $partnerPersonalTransactions = Transaction::where('user_id', $partner->id)
            ->where('wallet_type', 'personal')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        // ── GRAFIK 7 HARI (pemasukan & pengeluaran pribadi pasangan) ──
        $partnerChartLabels  = [];
        $partnerChartIncome  = [];
        $partnerChartExpense = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $partnerChartLabels[]  = $day->format('D, d/m');
            $partnerChartIncome[]  = Transaction::where('user_id', $partner->id)->where('wallet_type', 'personal')->where('type', 'income')->whereDate('date', $day)->sum('amount');
            $partnerChartExpense[] = Transaction::where('user_id', $partner->id)->where('wallet_type', 'personal')->where('type', 'expense')->whereDate('date', $day)->sum('amount');
        }

        return view('partner', compact(
            'partner',
            'partnerPersonalBalance',
            'partnerPersonalIncome',
            'partnerPersonalExpenseMonth',
            'partnerPersonalTransactions',
            'partnerChartLabels',
            'partnerChartIncome',
            'partnerChartExpense'
        ));
    }
}

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PartnerController;
use Illuminate\Support\Facades\Route;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FinancialGoalController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\WeeklyReportController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $userId = Auth::id();
    $month  = Carbon::now()->month;
    $year   = Carbon::now()->year;

    // ── PRIBADI ──────────────────────────────────────
    // Saldo bersih pribadi (semua waktu)
    $personalIncomeAll  = Transaction::where('user_id', $userId)->where('wallet_type', 'personal')->where('type', 'income')->sum('amount');
    $personalExpenseAll = Transaction::where('user_id', $userId)->where('wallet_type', 'personal')->where('type', 'expense')->sum('amount');
    $personalBalance    = $personalIncomeAll - $personalExpenseAll;

    // Pemasukan & pengeluaran pribadi bulan ini
    $personalIncome  = Transaction::where('user_id', $userId)->where('wallet_type', 'personal')->where('type', 'income')->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
    $personalExpense = Transaction::where('user_id', $userId)->where('wallet_type', 'personal')->where('type', 'expense')->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');

    // ── BERSAMA ──────────────────────────────────────
    // Saldo bersih bersama (semua waktu)
    $sharedIncomeAll  = Transaction::where('wallet_type', 'shared')->where('type', 'income')->sum('amount');
    $sharedExpenseAll = Transaction::where('wallet_type', 'shared')->where('type', 'expense')->sum('amount');
    $totalShared      = $sharedIncomeAll - $sharedExpenseAll;

    // Pemasukan & pengeluaran bersama bulan ini
    $sharedIncome  = Transaction::where('wallet_type', 'shared')->where('type', 'income')->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
    $sharedExpense = Transaction::where('wallet_type', 'shared')->where('type', 'expense')->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');

    // ── RIWAYAT ──────────────────────────────────────
    $personalTransactions = Transaction::where('user_id', $userId)->where('wallet_type', 'personal')->orderBy('date', 'desc')->orderBy('created_at', 'desc')->take(10)->get();
    $sharedTransactions   = Transaction::where('wallet_type', 'shared')->orderBy('date', 'desc')->orderBy('created_at', 'desc')->take(10)->get();

    // ── GRAFIK 7 HARI ─────────────────────────────────
    $chartLabels        = [];
    $personalChartIncome  = [];
    $personalChartExpense = [];
    $sharedChartIncome    = [];
    $sharedChartExpense   = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i);
        $chartLabels[] = $date->format('D, d/m');

        $personalChartIncome[]  = Transaction::where('user_id', $userId)->where('wallet_type', 'personal')->where('type', 'income')->whereDate('date', $date)->sum('amount');
        $personalChartExpense[] = Transaction::where('user_id', $userId)->where('wallet_type', 'personal')->where('type', 'expense')->whereDate('date', $date)->sum('amount');
        $sharedChartIncome[]    = Transaction::where('wallet_type', 'shared')->where('type', 'income')->whereDate('date', $date)->sum('amount');
        $sharedChartExpense[]   = Transaction::where('wallet_type', 'shared')->where('type', 'expense')->whereDate('date', $date)->sum('amount');
    }

    return view('dashboard', compact(
        'personalBalance', 'personalIncome', 'personalExpense',
        'totalShared', 'sharedIncome', 'sharedExpense',
        'personalTransactions', 'sharedTransactions',
        'chartLabels',
        'personalChartIncome', 'personalChartExpense',
        'sharedChartIncome', 'sharedChartExpense'
    ));
})->middleware(['auth'])->name('dashboard');

// --- KELOMPOK RUTE YANG WAJIB LOGIN ---
Route::middleware('auth')->group(function () {
    // Profile (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Transaksi & Goals
    Route::resource('transactions', TransactionController::class);
    Route::resource('goals', FinancialGoalController::class)->only(['index', 'store']);

    // Lihat catatan keuangan pasangan (read-only)
    Route::get('/partner/view', [PartnerController::class, 'view'])->name('partner.view');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/report/weekly', [WeeklyReportController::class, 'index'])->name('report.weekly');

    Route::post('/goals/progress', [FinancialGoalController::class, 'addProgress'])->name('goals.addProgress');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions',       [TransactionController::class, 'store'])->name('transactions.store');
});

require __DIR__.'/auth.php';

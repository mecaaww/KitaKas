<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\FinancialGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        // Nanti kita buat untuk halaman riwayat lengkap
    }

    public function create()
    {
        // Menampilkan form tambah transaksi
        $goals = FinancialGoal::orderBy('created_at', 'desc')->get();
        return view('transactions.create', compact('goals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'wallet_type' => 'required|in:shared,personal',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'split_ratio' => 'nullable|string', // Contoh: "50:50"
        ]);

        $validated['user_id'] = Auth::id();

        // LOGIKA SPLIT BILL (Fitur 5)
        if ($request->filled('split_ratio') && $request->type === 'expense') {
            if ($request->split_ratio === '50:50') {
                $halfAmount = $request->amount / 2;

                // 1. Simpan pengeluaran pribadi si pembayar (setengahnya)
                Transaction::create([
                    'user_id' => Auth::id(),
                    'date' => $request->date,
                    'type' => 'expense',
                    'wallet_type' => 'personal',
                    'amount' => $halfAmount,
                    'description' => $request->description . ' (Split 50%)',
                ]);

                // 2. Simpan setengahnya lagi sebagai pengeluaran dompet bersama
                Transaction::create([
                    'user_id' => Auth::id(),
                    'date' => $request->date,
                    'type' => 'expense',
                    'wallet_type' => 'shared',
                    'amount' => $halfAmount,
                    'description' => $request->description . ' (Patungan)',
                ]);

                return redirect()->route('dashboard')->with('success', 'Berhasil! Tagihan di-split 50:50.');
            }
        }

        // Jika tidak split, simpan normal
        Transaction::create($validated);

        return redirect()->route('dashboard')->with('success', 'Transaksi berhasil dicatat!');
    }

    // Biarkan fungsi show, edit, update, destroy kosong untuk saat ini...
}

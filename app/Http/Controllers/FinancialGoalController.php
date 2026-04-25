<?php

namespace App\Http\Controllers;

use App\Models\FinancialGoal;
use Illuminate\Http\Request;

class FinancialGoalController extends Controller
{
    // Menampilkan halaman daftar tujuan keuangan
    public function index()
    {
        $goals = FinancialGoal::orderBy('created_at', 'desc')->get();
        return view('goals.index', compact('goals'));
    }

    // Menyimpan tujuan keuangan baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'deadline'      => 'nullable|date',
        ]);

        $validated['current_amount'] = 0;

        FinancialGoal::create($validated);

        return back()->with('success', 'Tujuan keuangan bersama berhasil ditambahkan!');
    }

    // Menambah progress (current_amount) ke target yang dipilih
    public function addProgress(Request $request)
    {
        $request->validate([
            'goal_id'         => 'required|exists:financial_goals,id',
            'progress_amount' => 'required|numeric|min:1',
        ]);

        $goal = FinancialGoal::findOrFail($request->goal_id);

        // Tambah amount, tidak melebihi target
        $goal->current_amount = min(
            $goal->target_amount,
            $goal->current_amount + $request->progress_amount
        );
        $goal->save();

        $isComplete = $goal->current_amount >= $goal->target_amount;

        return redirect()->route('transactions.create')
            ->with('success', $isComplete
                ? "🎉 Selamat! Target \"{$goal->name}\" sudah tercapai!"
                : "Progress untuk \"{$goal->name}\" berhasil ditambahkan!"
            );
    }
}

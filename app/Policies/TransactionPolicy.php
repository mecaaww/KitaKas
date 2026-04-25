<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    /**
     * Semua user (Rizky & Dinda) diizinkan melihat daftar transaksi.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Semua user diizinkan melihat detail transaksi apa pun (sesuai permintaan).
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return true;
    }

    /**
     * Semua user diizinkan membuat transaksi baru.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Logika Utama: Siapa yang boleh mengedit transaksi?
     */
    public function update(User $user, Transaction $transaction): bool
    {
        // Jika ini dompet bersama, keduanya boleh edit
        if ($transaction->wallet_type === 'shared') {
            return true;
        }

        // Jika ini dompet personal, HANYA pemilik asli yang boleh edit
        return $user->id === $transaction->user_id;
    }

    /**
     * Logika Utama: Siapa yang boleh menghapus transaksi?
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        // Aturannya sama persis dengan update
        if ($transaction->wallet_type === 'shared') {
            return true;
        }

        return $user->id === $transaction->user_id;
    }
}

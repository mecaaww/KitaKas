<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Kolom-kolom yang diizinkan untuk diisi secara massal
    protected $fillable = [
        'user_id',
        'type',
        'wallet_type',
        'amount',
        'date',
        'category',
        'description',
        'split_ratio',
        'comment'
    ];

    // Memastikan kolom date diperlakukan sebagai objek tanggal (Carbon)
    protected $casts = [
        'date' => 'date',
    ];

    // RELASI: Transaksi ini milik siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

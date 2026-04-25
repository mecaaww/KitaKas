<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'gender',
        'password',
        'theme',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * FIX: Accessor — kalau kolom `theme` di DB null/kosong,
     * otomatis fallback berdasarkan gender.
     * Female → pink, Male → blue.
     */
    public function getThemeAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        return $this->gender === 'female' ? 'pink' : 'blue';
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}

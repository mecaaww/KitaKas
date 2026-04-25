<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat User Male (Rizky)
        User::create([
            'name' => 'Laki-laki',
            'gender' => 'male',
            'password' => Hash::make('password123'), // Password untuk Rizky
            'theme' => 'blue'
        ]);

        // Membuat User Female (Dinda)
        User::create([
            'name' => 'Perempuan',
            'gender' => 'female',
            'password' => Hash::make('password123'), // Password untuk Dinda
            'theme' => 'pink'
        ]);
    }
}

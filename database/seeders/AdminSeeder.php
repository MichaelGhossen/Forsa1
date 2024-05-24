<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Michael',
            'last_name'=>'Ghossen',
            'email' => 'michaelghossen@gmail.com',
            'password' => Hash::make('12345678'),
            'image'=>'null',
            //'cv'=>'NULL',
            'user_type' => 'admin'
        ]);
    }
}

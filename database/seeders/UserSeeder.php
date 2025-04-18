<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // membuat user deafult di table user
        User::create([
            'username' => 'admin',
            'password' => Hash::make('123456'),
            'name' => 'Admin ne',
            'token' => 'test_token'
        ]);

        User::create(
        [
            'username' => 'admin2',
            'password' => Hash::make('123456'),
            'name' => 'admin2',
            'token' => 'test_token_2'
        ]);
    }
}

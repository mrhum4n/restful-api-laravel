<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'admin')->first();
        Contact::create([
            'first_name' => 'kedux',
            'last_name' => 'garong',
            'email' => 'kedux@gmail.com',
            'phone' => '08123456789',
            'user_id' => $user->id
        ]);
    }
}

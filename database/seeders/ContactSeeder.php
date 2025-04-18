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

        // create many contacts
        $i = 0;
        while ($i < 500) {
            if ($i >= 0 && $i <= 100) { // membuat total contact name 'budi' sebanyak 101 data
                Contact::create([
                    'first_name' => 'budi'.$i,
                    'last_name' => 'budi'.$i,
                    'email' => 'budi'.$i.'@gmail.com',
                    'phone' => '+26B123456789'.$i,
                    'user_id' => $user->id
                ]);
            }else if ($i >= 101 && $i <= 200) { // membuat total contact name 'anton' sebanyak 100 data
                Contact::create([
                    'first_name' => 'anton'.$i,
                    'last_name' => 'anton'.$i,
                    'email' => 'anton'.$i.'@gmail.com',
                    'phone' => '+77A123456789'.$i,
                    'user_id' => $user->id
                ]);
            }else if ($i >= 201 && $i <= 400) { // membuat total contact name 'kocong' sebanyak 200 data
                Contact::create([
                    'first_name' => 'kocong'.$i,
                    'last_name' => 'kocong'.$i,
                    'email' => 'kocong'.$i.'@gmail.com',
                    'phone' => '+11K123456789'.$i,
                    'user_id' => $user->id
                ]);
            }else if ($i >= 401 && $i <= 499) { // membuat total contact name 'leak' sebanyak 99 data
                Contact::create([
                    'first_name' => 'leak'.$i,
                    'last_name' => 'leak'.$i,
                    'email' => 'leak'.$i.'@gmail.com',
                    'phone' => '+69L123456789'.$i,
                    'user_id' => $user->id
                ]);
            }

            $i++;
        }
    }
}

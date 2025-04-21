<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $contact = Contact::query()->orderBy('id', 'desc')->limit(1)->first();
        // Address::create([
        //     'contact_id' => $contact->id,
        //     'street' => 'Jl. Joh Kelod',
        //     'city' => 'Denpasar',
        //     'province' => 'Bali',
        //     'country' => 'Indonesia',
        //     'zip_code' => '12345'
        // ]);

        // call factory
        $contacts = Contact::query()->get();

        Address::factory()
            ->count(5)
            ->make()
            ->each(function($address) use ($contacts) {
                $address->contact_id = $contacts->random()->id;
                $address->save();
            });
    }
}

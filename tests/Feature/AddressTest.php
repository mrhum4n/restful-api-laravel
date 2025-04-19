<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->assertNotNull($contact);

        $this->post('/api/contact/'.$contact->id.'/address', [
            'street' => 'Jl. Pegesangan',
            'city' => 'Badung',
            'province' => 'Bali',
            'country' => 'Indonesia',
            'zip_code' => '12345',
        ], 
        [
            'Authorization' => 'test_token'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'street' => 'Jl. Pegesangan',
                    'city' => 'Badung',
                    'province' => 'Bali',
                    'country' => 'Indonesia',
                    'zip_code' => '12345',
                ]
            ]);
    }

    public function testCreateFailed() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->assertNotNull($contact);

        $this->post('/api/contact/'.$contact->id.'/address', [
            'street' => 'Jl. Pegesangan',
            'city' => 'Badung',
            'province' => 'Bali',
            'country' => 'Indonesia',
            'zip_code' => '4654657346534856475637',
        ], 
        [
            'Authorization' => 'test_token'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'zip_code' => [
                        'The zip code field must not be greater than 10 characters.'
                    ]
                ]
            ]);
    }

    public function testCreateContactNotFound() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->orderBy('id', 'desc')->limit(1)->first();
        $this->assertNotNull($contact);

        $this->post('/api/contact/'.($contact->id + 1).'/address', [
            'street' => 'Jl. Pegesangan',
            'city' => 'Badung',
            'province' => 'Bali',
            'country' => 'Indonesia',
            'zip_code' => '12345',
        ], 
        [
            'Authorization' => 'test_token'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'data not found!'
                    ]
                ]
            ]);
    }

    public function testGetSuccess() {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();

        $this->get('/api/contact/'.$address->contact_id.'/address/'.$address->id, [
            'Authorization' => 'test_token'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'Jl. Joh Kelod',
                    'city' => 'Denpasar',
                    'province' => 'Bali',
                    'country' => 'Indonesia',
                    'zip_code' => '12345'
                ]
            ]);
    }

    public function testGetNotFound() {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();

        $this->get('/api/contact/'.$address->contact_id.'/address/'.($address->id + 1), [
            'Authorization' => 'test_token'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'data not found!'
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess() {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put('/api/contact/'.$address->contact_id.'/address/'.$address->id, 
            [
                'city' => 'Kuta Rock City'
            ],
            [
                'Authorization' => 'test_token'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'Jl. Joh Kelod',
                    'city' => 'Kuta Rock City',
                    'province' => 'Bali',
                    'country' => 'Indonesia',
                    'zip_code' => '12345'
                ]
            ]);
    }

    public function testUpdateFailed() {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put('/api/contact/'.$address->contact_id.'/address/'.$address->id, 
            [
                'city' => 'arah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kadenarah ape kaden'
            ],
            [
                'Authorization' => 'test_token'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'city' => [
                        'The city field must not be greater than 100 characters.'
                    ]
                ]
            ]);
    }

    public function testUpdateNotFound() {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put('/api/contact/'.$address->contact_id.'/address/'.($address->id + 1), 
            [
                'city' => 'Jakarta Keras'
            ],
            [
                'Authorization' => 'test_token'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'data not found!'
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess() {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete('/api/contact/'.$address->contact_id.'/address/'.$address->id, 
            [],
            [
                'Authorization' => 'test_token'
            ]
        )->assertStatus(200)
            ->assertJson([
                'error' => false,
                'message' => 'contact has been deleted!'
            ]);
    }

    public function testDeleteNotFound() {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete('/api/contact/'.$address->contact_id.'9999'.'/address/'.$address->id + 1, 
            [],
            [
                'Authorization' => 'test_token'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'data not found!'
                    ]
                ]
            ]);
    }

    public function testGetAllSuccess() {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->orderBy('id', 'desc')->limit(1)->first();

        $this->get('/api/contact/'.$contact->id.'/addresses', [
            'Authorization' => 'test_token'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'street' => 'Jl. Joh Kelod',
                        'city' => 'Denpasar',
                        'province' => 'Bali',
                        'country' => 'Indonesia',
                        'zip_code' => '12345'
                    ]
                ]
            ]);
    }

    public function testGetAllContactNotFound() {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->orderBy('id', 'desc')->limit(1)->first();

        $this->get('/api/contact/'.($contact->id + 1).'/addresses', [
            'Authorization' => 'test_token'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'data not found!'
                    ]
                ]
            ]);
    }
}

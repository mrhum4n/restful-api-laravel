<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateSuccess() {
        $this->seed([UserSeeder::class]);

        $this->post('api/contact', [
            'first_name' => 'kentod',
            'last_name' => 'cg',
            'email' => 'cg@gmail.com',
            'phone' => '08123456789'
        ], [
            'Authorization' => 'test_token'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'kentod',
                    'last_name' => 'cg',
                    'email' => 'cg@gmail.com',
                    'phone' => '08123456789'
                ]
            ]);
    }

    public function testCreateFailed() {
        $this->seed([UserSeeder::class]);

        $this->post('api/contact', [
            'first_name' => '',
            'last_name' => 'cg',
            'email' => 'cg@',
            'phone' => '08123456789'
        ], [
            'Authorization' => 'test_token'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ],
                    'email' => [
                        'The email field must be a valid email address.'
                    ]
                ]
            ]);
    }

    public function testCreateUnauthorize() {
        $this->seed([UserSeeder::class]);

        $this->post('api/contact', [
            'first_name' => 'kentod',
            'last_name' => 'cg',
            'email' => 'cg@gmail.com',
            'phone' => '08123456789'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetSuccess() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contactLastCreated = Contact::query()->limit(1)->first();

        $this->get('/api/contact/'.$contactLastCreated->id, [
            'Authorization' => 'test_token'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'first_name' => 'kedux',
                'last_name' => 'garong',
                'email' => 'kedux@gmail.com',
                'phone' => '08123456789'
            ]
        ]);
    }

    public function testGetNotFound() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contactLastCreated = Contact::query()->limit(1)->first();

        $this->get('/api/contact/'.($contactLastCreated->id + 1), [
            'Authorization' => 'test_token'
        ])->assertStatus(404)
        ->assertJson([
            'errors' => [
                'message' => [
                    'contact not found!'
                ]
            ]
        ]);
    }

    public function testGetOtherUserContact() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contactLastCreated = Contact::query()->limit(1)->first();

        $this->get('/api/contact/'.($contactLastCreated->id + 1), [
            'Authorization' => 'test_token_2'
        ])->assertStatus(404)
        ->assertJson([
            'errors' => [
                'message' => [
                    'contact not found!'
                ]
            ]
        ]);
    }

    public function testUpdateSuccess() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contactLastCreated = Contact::query()->limit(1)->first();

        $this->put('/api/contact/'.$contactLastCreated->id, [
            'first_name' => 'kedux2',
            'last_name' => 'garong2',
            'email' => 'kedux2@gmail.com',
            'phone' => '081234567892'
        ],
        [
            'Authorization' => 'test_token'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'first_name' => 'kedux2',
                'last_name' => 'garong2',
                'email' => 'kedux2@gmail.com',
                'phone' => '081234567892'
            ]
        ]);
    }

    public function testUpdateValidationError() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contactLastCreated = Contact::query()->limit(1)->first();

        $this->put('/api/contact/'.$contactLastCreated->id, [
            'first_name' => '',
            'last_name' => 'garong2',
            'email' => 'kedux2@gmail.com',
            'phone' => '081234567892'
        ],
        [
            'Authorization' => 'test_token'
        ])->assertStatus(400)
        ->assertJson([
            'errors' => [
                'first_name' => [
                    'The first name field is required.'
                ]
            ]
        ]);
    }

    public function testDeleteSuccess() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contactLastCreated = Contact::query()->limit(1)->first();

        $this->delete('/api/contact/'.$contactLastCreated->id, [],
        [
            'Authorization' => 'test_token'
        ])->assertStatus(200)
        ->assertJson([
            'error' => false,
            'message' => 'contact has been deleted!'
        ]);
    }

    public function testDeleteFailed() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contactLastCreated = Contact::query()->limit(1)->first();

        $this->delete('/api/contact/'.($contactLastCreated->id + 1), [],
        [
            'Authorization' => 'test_token'
        ])->assertStatus(404)
        ->assertJson([
            'errors' => [
                'message' => [
                    'contact not found!'
                ]
            ]
        ]);
    }

    public function testSearchByName() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $response = $this->get('/api/contacts?name=kocong', [
            'Authorization' => 'test_token'
        ])->assertStatus(200);

        $this->assertEquals(10, count($response['data']));
        $this->assertEquals(200, $response['meta']['total']);
    }

    public function testSearchByEmail() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $response = $this->get('/api/contacts?email=budi', [
            'Authorization' => 'test_token'
        ])->assertStatus(200);

        $this->assertEquals(10, count($response['data']));
        $this->assertEquals(101, $response['meta']['total']);
    }

    public function testSearchByPhone() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $response = $this->get('/api/contacts?phone=+69L', [
            'Authorization' => 'test_token'
        ])->assertStatus(200);

        $this->assertEquals(10, count($response['data']));
        $this->assertEquals(99, $response['meta']['total']);
    }

    public function testSearchNotFound() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $response = $this->get('/api/contacts?name=singade', [
            'Authorization' => 'test_token'
        ])->assertStatus(200);

        $this->assertEquals(0, count($response['data']));
        $this->assertEquals(0, $response['meta']['total']);
    }

    public function testSearchWithPage() {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $response = $this->get('/api/contacts?size=50&page=10', [
            'Authorization' => 'test_token'
        ])->assertStatus(200);

        $this->assertEquals(50, count($response['data']));
        $this->assertEquals(501, $response['meta']['total']);
        $this->assertEquals(10, $response['meta']['current_page']);
    }

}

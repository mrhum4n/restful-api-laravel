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
}

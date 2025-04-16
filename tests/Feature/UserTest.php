<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess() {
        $this->post('api/register', [
            'username' => 'bracux',
            'password' => '123456',
            'name' => 'bracux kleg'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'bracux',
                    'name' => 'bracux kleg'
                ]
            ]);
    }

    public function testRegisterFailed() {
        $this->post('api/register', [
            'username' => '',
            'password' => '',
            'name' => ''
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'The username field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ],
                    'name' => [
                        'The name field is required.'
                    ]
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyExists() {
        // membuat user
        $this->testRegisterSuccess();
        
        $this->post('api/register', [
            'username' => 'bracux',
            'password' => '123456',
            'name' => 'bracux kleg'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'username already registered'
                    ]
                ]
            ]);
    }

    public function testLoginSuccess() {
        // running user seed
        $this->seed([UserSeeder::class]);

        // membuat request
        $this->post('api/login', [
            'username' => 'admin',
            'password' => '123456'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'admin',
                    'name' => 'Admin ne'
                ]
            ]);

        // cek apakah token sudah tersedia atau belum
        $user = User::where('username', 'admin')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound() {
        $this->post('api/login', [
            'username' => 'test',
            'password' => '123456'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'username or password wrong!'
                    ]
                ]
            ]);
    }

    public function testLoginFailedPasswordWrong() {
        // membuat seed user
        $this->seed([UserSeeder::class]);

        // membuat request
        $this->post('api/login', [
            'username' => 'admin',
            'password' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'username or password wrong!'
                    ]
                ]
            ]);
    }

    public function testGetUserSuccess() {
        // membuat user seed
        $this->seed([UserSeeder::class]);

        // membuat request
        $this->get('api/user/current', [
            'Authorization' => 'test_token'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'admin',
                    'name' => 'Admin ne',
                    'token' => 'test_token'
                ]
            ]);
    }

    public function testGetUserUnauthorize() {
        // membuat user seed
        $this->seed([UserSeeder::class]);

        // membuat request
        $this->get('api/user/current')
        ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetUserWithInvalidToken() {
        // membuat user seed
        $this->seed([UserSeeder::class]);

        // membuat request
        $this->get('api/user/current', [
            'Authorization' => 'token pelih'
        ])
        ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdateNameSuccess() {
        $this->seed([UserSeeder::class]);

        $oldUser = User::where('username', 'admin')->first();

        $this->put('api/user/current', [
            'name' => 'admin baru'
        ], [
            'Authorization' => 'test_token'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'admin',
                    'name' => 'admin baru',
                    'token' => 'test_token'
                ]
            ]);
        
        $newUser = User::where('username', 'admin')->first();

        $this->assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdatePasswordSuccess() {
        $this->seed([UserSeeder::class]);

        $oldUser = User::where('username', 'admin')->first();

        $this->put('api/user/current', [
            'password' => '123'
        ], [
            'Authorization' => 'test_token'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'admin',
                    'name' => 'Admin ne',
                    'token' => 'test_token'
                ]
            ]);
        
        $newUser = User::where('username', 'admin')->first();

        $this->assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameFailed() {
        $this->seed([UserSeeder::class]);

        $this->put('api/user/current', [
            'name' => 'klegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegciklegci'
        ], [
            'Authorization' => 'test_token'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field must not be greater than 100 characters.'
                    ]
                ]
            ]);
    }

    public function testUpdateUserUnauthorize() {
        $this->seed([UserSeeder::class]);

        $this->put('api/user/current', [
            'name' => 'admin bangsat'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }
}

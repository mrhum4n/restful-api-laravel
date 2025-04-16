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
}

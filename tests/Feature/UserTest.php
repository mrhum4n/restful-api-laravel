<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess() {
        $this->post('api/user', [
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
        $this->post('api/user', [
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
        
        $this->post('api/user', [
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
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_register()
    {
        $user = User::factory()->make();

        $response = $this->postJson('api/v1/auth/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);

        $response->assertJsonStructure([
            'token',
            'user' => [
                'id',
                'name',
                'email',
            ],
        ]);

        $response->assertJsonPath('user.email', $user->email);
    }

    public function test_duplicate_email_fails_registration()
    {
        $user = User::factory()->create();

        $response = $this->post('api/v1/auth/register', [
            'name' => $user->name.' Aja',
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_login()
    {
        $password = 'password';

        $user = User::factory()->create(
            ['password' => Hash::make($password)],
        );

        $response = $this->postJson('api/v1/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
            'user' => [
                'id',
                'name',
                'email',
            ],
        ]);

        $response->assertJsonPath('user.email', $user->email);
    }

    public function test_wrong_password_cannot_login()
    {
        $password = 'password';

        $user = User::factory()->create(
            ['password' => Hash::make($password)],
        );

        $response = $this->post('api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ]);

        $response->assertStatus(401);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_pode_se_cadastrar()
    {
        $response = $this->postJson('/api/register', [

            'name' => 'Gabriel',

            'email' => 'gabriel@email.com',

            'password' => '12345678',

            'password_confirmation' => '12345678'

        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'user',
            'token'
        ]);
    }

    public function test_usuario_pode_fazer_login()
    {
        User::factory()->create([

            'email' => 'admin@email.com',

            'password' => bcrypt('123456')

        ]);

        $response = $this->postJson('/api/login', [

            'email' => 'admin@email.com',

            'password' => '123456'

        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'token' => true
        ]);
    }

    public function test_login_invalido()
    {
        $response = $this->postJson('/api/login', [

            'email' => 'teste@email.com',

            'password' => 'errada'

        ]);

        $response->assertStatus(401);
    }
}

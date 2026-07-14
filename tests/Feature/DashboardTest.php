<?php

namespace Tests\Feature;

use App\Models\Contato;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_dashboard()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Contato::factory()->count(8)->create([

            'user_id' => $user->id

        ]);

        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(200);

        $response->assertJsonStructure([

            'total_contatos',

            'favoritos',

            'mes',

            'ultimos'

        ]);
    }
}

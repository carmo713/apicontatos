<?php

namespace Tests\Feature;

use App\Models\Contact;
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

        Contact::factory()->count(8)->create([

            'user_id' => $user->id

        ]);

        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(200);

        $response->assertJsonStructure([

            'total_Contacts',

            'favorites',

            'mes',

            'ultimos'

        ]);
    }
}

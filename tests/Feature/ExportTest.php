<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\Export;

class ExportTest extends TestCase
{
    public function test_solicitar_exportacao()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/exports', [

            'formato' => 'csv'

        ]);

        $response->assertStatus(202);

        $this->assertDatabaseHas('exports', [

            'status' => 'Pendente'

        ]);
    }
    public function test_listar_exportacoes()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Export::factory()->count(3)->create([

            'user_id' => $user->id

        ]);

        $response = $this->getJson('/api/exports');

        $response->assertStatus(200);
    }
}

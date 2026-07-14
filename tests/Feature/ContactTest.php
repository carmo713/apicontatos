<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Contato;
use Laravel\Sanctum\Sanctum;

class ContactTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_criar_contato()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/contacts', [

            'name' => 'Maria',

            'telefone' => '38999999999'

        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('contacts', [

            'name' => 'Maria'

        ]);
    }

    public function test_listar_contatos()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Contato::factory()->count(5)->create([

            'user_id' => $user->id

        ]);

        $response = $this->getJson('/api/contacts');

        $response->assertStatus(200);
    }

    public function test_atualizar_contato()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $contact = Contato::factory()->create([

            'user_id' => $user->id

        ]);

        $response = $this->putJson("/api/contacts/$contact->id", [

            'name' => 'Novo Nome'

        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('contacts', [

            'name' => 'Novo Nome'

        ]);
    }

    public function test_excluir_contato()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $contact = Contato::factory()->create([

            'user_id' => $user->id

        ]);

        $response = $this->deleteJson("/api/contacts/$contact->id");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('contacts', [

            'id' => $contact->id

        ]);
    }
    public function test_favoritar_contato()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $contact = Contato::factory()->create([

            'user_id' => $user->id,

            'favorito' => false

        ]);

        $response = $this->patchJson(

            "/api/contacts/$contact->id/favorite"

        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('contacts', [

            'favorito' => true

        ]);
    }
     public function test_usuario_nao_pode_ver_contato_de_outro_usuario()
    {
        $usuario1 = User::factory()->create();

        $usuario2 = User::factory()->create();

        $contact = Contato::factory()->create([
            'user_id' => $usuario2->id
        ]);

        Sanctum::actingAs($usuario1);

        $response = $this->getJson("/api/contacts/{$contact->id}");

        $response->assertStatus(404);
    }
}


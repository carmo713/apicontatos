<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use Laravel\Sanctum\Sanctum;

class ContactTest extends TestCase
{
    use RefreshDatabase;


    public function test_criar_Contact()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/contacts', [

            'name' => 'Maria',

            'phone' => '38999999999',

            'email' => 'maria@example.com'

        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('contacts', [

            'name' => 'Maria'

        ]);
    }

    public function test_listar_Contacts()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Contact::factory()->count(5)->create([

            'user_id' => $user->id

        ]);

        $response = $this->getJson('/api/contacts');

        $response->assertStatus(200);
    }

    public function test_atualizar_Contact()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $contact = Contact::factory()->create([

            'user_id' => $user->id

        ]);

        $response = $this->putJson("/api/contacts/$contact->id", [

            'name' => 'Novo name'

        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('contacts', [

            'name' => 'Novo name'

        ]);
    }

    public function test_excluir_Contact()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $contact = Contact::factory()->create([

            'user_id' => $user->id

        ]);

        $response = $this->deleteJson("/api/contacts/$contact->id");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('contacts', [

            'id' => $contact->id

        ]);
    }
    public function test_favoritar_Contact()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $contact = Contact::factory()->create([

            'user_id' => $user->id,

            'favorite' => false

        ]);

        $response = $this->patchJson(

            "/api/contacts/$contact->id/favorite"

        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('contacts', [

            'favorite' => true

        ]);
    }
     public function test_usuario_nao_pode_ver_Contact_de_outro_usuario()
    {
        $usuario1 = User::factory()->create();

        $usuario2 = User::factory()->create();

        $contact = Contact::factory()->create([
            'user_id' => $usuario2->id
        ]);

        Sanctum::actingAs($usuario1);

        $response = $this->getJson("/api/contacts/{$contact->id}");

        $response->assertStatus(404);
    }
}


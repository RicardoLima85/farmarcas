<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->post('http://localhost:8000/criar-evento', [
            'title' => 'Exemplo de Título',
            'description' => 'Exemplo de Descrição',
            'start' => '2023-09-20',
            'end' => '2023-09-30',
            'usr_responsavel' => 'kainangabriel2019@gmail.com',
        ]);

        $response->assertStatus(201);
    }

        public function test_edit_event()
    {
        $event = factory()->create(); // Suponha que você tenha um evento criado

        $data = [
            'title' => 'Evento Editado',
            'description' => 'Descrição Editada',
            'start' => '2023-11-01',
            'end' => '2023-11-10',
            'usr_responsavel' => 'test@example.com',
        ];

        $response = $this->put("http://localhost:8000/editar-evento/{$event->id}", $data);

        $response->assertStatus(200) // Deve retornar código 200 (OK)
            ->assertJson(['message' => 'Recurso editado com sucesso']);
    }


        public function test_delete_event()
    {
        $event = factory()->create(); // Suponha que você tenha um evento criado

        $response = $this->delete("http://localhost:8000/excluir-evento/{$event->id}");

        $response->assertStatus(204); // Deve retornar código 204 (Sem conteúdo)
    }
}

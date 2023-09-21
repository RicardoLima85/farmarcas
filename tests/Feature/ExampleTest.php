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
    public function test_user_registration()
    {
        $response = $this->post('http://localhost:8000/register', [
            'name' => 'Johnss Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
    
        $response->assertStatus(302);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token-name')->plainTextToken;
    
            return response()->json(['token' => $token], 200);
        }
    
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function test_user_login()
    {
        $response = $this->post('http://localhost:8000/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);
    
        $response->assertStatus(302);
    
        // Check for the 'laravel_session' cookie
        $response->assertCookie('laravel_session');
    
        // Extract the cookie value (token)
        $token = $response->cookie('laravel_session');
    
        return $token;
    }

    public function test_create_event()
    {
        $token = $this->test_user_login();

        // Manually create an event
        $response = $this->json('POST', 'http://localhost:8000/criar-evento', [
            'title' => 'Exemplo de Título',
            'description' => 'Exemplo de Descrição',
            'start' => '2023-09-20',
            'end' => '2023-09-30',
            'usr_responsavel' => 'john@example.com',
        ], ['Authorization' => "Bearer $token"]);
    
        $response->assertStatus(302);
    }

<<<<<<< HEAD
    public function test_edit_event()
=======
        public function test_get_event()
    {
        $event = factory()->create(); // Suponha que você tenha um evento criado

        $response = $this->get("http://localhost:8000/visualizar-evento/{$event->id}");

        $response->assertStatus(200) // Deve retornar código 200 (OK)
            ->assertSee($event->title) // Verifica se o título do evento está presente na resposta
            ->assertSee($event->description); // Verifica se a descrição do evento está presente na resposta
    }

        public function test_edit_event()
>>>>>>> 38aef28765817a91db042096e268f529579469b1
    {
        $token = $this->test_user_login();

        // Manually create an event
        $event = Event::create([
            'title' => 'Evento para Edição',
            'description' => 'Descrição do Evento',
            'start' => '2023-09-20',
            'end' => '2023-09-30',
            'usr_responsavel' => 'john@example.com',
        ]);

        $data = [
            'title' => 'Evento Editado',
            'description' => 'Descrição Editada',
            'start' => '2023-11-01',
            'end' => '2023-11-10',
            'usr_responsavel' => 'john@example.com',
        ];
    
        $response = $this->put("http://localhost:8000/editar-evento/{$event->id}", $data, ['Authorization' => "Bearer $token"]);
    
        $response->assertStatus(302);
    }

    public function test_delete_event()
    {
        $token = $this->test_user_login();

        // Manually create an event
        $event = Event::create([
            'title' => 'Evento para Exclusão',
            'description' => 'Descrição do Evento',
            'start' => '2023-09-20',
            'end' => '2023-09-30',
            'usr_responsavel' => 'john@example.com',
        ]);
    
        $response = $this->delete("http://localhost:8000/excluir-evento/{$event->id}", [], ['Authorization' => "Bearer $token"]);
    
        $response->assertStatus(302);
    }
}

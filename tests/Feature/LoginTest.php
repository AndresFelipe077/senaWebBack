<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LoginTest extends TestCase
{

  // use RefreshDatabase;

  /**
   * A basic feature test example.
   *
   * @return void
   */
  public function test_example()
  {
    
    $existingUser = User::find(2); // Cambia el ID según tus necesidades

    // Hacer una solicitud POST a la ruta de inicio de sesión de la API utilizando las credenciales del usuario existente
    $response = $this->json('POST', '/api/login', [
        'email' => $existingUser->email,
        'password' => '123', // Cambia esto según la contraseña del usuario
    ]);

    // Verificar que la solicitud se haya realizado con éxito
    $response->assertStatus(400);

    // Opcional: Verificar la estructura de la respuesta JSON
    /*$response->assertJsonStructure([
      'access_token',
      'token_type',
      'expires_in',
    ]);*/

    // Opcional: Puedes agregar más aserciones para probar otros aspectos del inicio de sesión.
  }
}

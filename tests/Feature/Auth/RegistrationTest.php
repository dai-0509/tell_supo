<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Test123!@#Password',
            'password_confirmation' => 'Test123!@#Password',
        ]);

        // レスポンスをダンプしてデバッグ
        if ($response->status() !== 302) {
            dump($response->getContent());
        }
        
        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}

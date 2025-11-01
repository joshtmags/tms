<?php
// tests/Feature/AuthControllerTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    public function test_user_can_login_with_correct_credentials()
    {
        $user = $this->create_user();

        $response = $this->postJson("/api/auth/login", [
            "email" => $user->email,
            "password" => "password",
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "success",
                "message",
                "data" => [
                    "user" => ["id", "name", "email"],
                    "access_token",
                    "token_type",
                ],
            ])
            ->assertJson([
                "success" => true,
                "message" => "Login successful",
            ]);
    }

    public function test_user_cannot_login_with_incorrect_credentials()
    {
        $response = $this->postJson("/api/auth/login", [
            "email" => "nonexistent@example.com",
            "password" => "wrongpassword",
        ]);

        $response->assertStatus(401)
            ->assertJson([
                "success" => false,
                "message" => "Invalid credentials",
            ]);
    }
}

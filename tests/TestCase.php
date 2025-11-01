<?php

namespace Tests;

use App\Models\Language;
use App\Models\TranslationTag;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling([
            AuthenticationException::class,
            ValidationException::class,
        ]);
    }

    protected function create_user(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            "name" => "Test User",
            "email" => "test@example.com",
            "password" => Hash::make("password"),
        ], $attributes));
    }

    protected function create_authenticated_user(array $attributes = []): array
    {
        $user = $this->create_user($attributes);
        $token = $user->createToken("test-token")->plainTextToken;

        return [
            "user" => $user,
            "token" => $token,
        ];
    }

    protected function with_authentication_headers(string $token): array
    {
        return [
            "Authorization" => "Bearer " . $token,
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ];
    }

    protected function create_test_languages(): void
    {
        Language::factory()->english()->create();
        Language::factory()->french()->create();
        Language::factory()->spanish()->create();
    }

    protected function create_test_tags(): void
    {
        TranslationTag::factory()->web()->create();
        TranslationTag::factory()->mobile()->create();
        TranslationTag::factory()->desktop()->create();
    }
}

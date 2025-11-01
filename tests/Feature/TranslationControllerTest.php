<?php
// tests/Feature/TranslationControllerTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Language;
use App\Models\TranslationGroup;
use App\Models\Translation;

class TranslationControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Language::factory()->english()->create();
        Language::factory()->french()->create();
    }

    public function test_authenticated_user_can_create_translation()
    {
        $auth = $this->create_authenticated_user();

        $translation_data = [
            "key" => "auth.login.header",
            "description" => "Login page header",
            "translations" => [
                [
                    "language_code" => "en",
                    "value" => "Welcome Back",
                ],
                [
                    "language_code" => "fr",
                    "value" => "Bon retour",
                ],
            ],
            "tags" => ["web", "auth"],
        ];

        $response = $this->postJson(
            "/api/translations",
            $translation_data,
            $this->with_authentication_headers($auth["token"])
        );

        $response->assertStatus(201)
            ->assertJsonStructure([
                "success",
                "data" => [
                    "id",
                    "key",
                    "description",
                    "translations",
                    "tags",
                ],
            ])
            ->assertJson([
                "success" => true,
                "data" => [
                    "key" => "auth.login.header",
                    "description" => "Login page header",
                ],
            ]);
    }

    public function test_authenticated_user_can_list_translations()
    {
        $auth = $this->create_authenticated_user();

        TranslationGroup::factory()
            ->has(Translation::factory()->count(2))
            ->count(3)
            ->create();

        $response = $this->getJson("/api/translations", $this->with_authentication_headers($auth["token"]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                "success",
                "data" => [
                    "data" => [
                        "*" => [
                            "id",
                            "key",
                            "description",
                            "translations",
                            "tags",
                        ],
                    ],
                ],
            ])
            ->assertJson([
                "success" => true,
            ]);
    }

    public function test_authenticated_user_can_get_translation_by_id()
    {
        $auth = $this->create_authenticated_user();

        $translation_group = TranslationGroup::factory()
            ->has(Translation::factory()->count(2))
            ->create();

        $response = $this->getJson(
            "/api/translations/{$translation_group->id}",
            $this->with_authentication_headers($auth["token"])
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                "success",
                "data" => [
                    "id",
                    "key",
                    "description",
                    "translations",
                    "tags",
                ],
            ])
            ->assertJson([
                "success" => true,
            ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_endpoints()
    {
        $response = $this->getJson("/api/translations");

        $response->assertStatus(401)
            ->assertJson([
                "message" => "Unauthenticated.",
            ]);
    }

    public function test_authenticated_user_can_update_translation()
    {
        $auth = $this->create_authenticated_user();

        $translation_group = TranslationGroup::factory()
            ->has(Translation::factory()->count(2))
            ->create();

        $update_data = [
            "key" => "auth.login.updated_header",
            "description" => "Updated login header",
            "translations" => [
                [
                    "language_code" => "en",
                    "value" => "Updated Welcome Back",
                ],
            ],
            "tags" => ["web", "mobile"],
        ];

        $response = $this->putJson(
            "/api/translations/{$translation_group->id}",
            $update_data,
            $this->with_authentication_headers($auth["token"])
        );

        $response->assertStatus(200)
            ->assertJson([
                "success" => true,
                "data" => [
                    "key" => "auth.login.updated_header",
                    "description" => "Updated login header",
                ],
            ]);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Language;
use App\Models\TranslationGroup;
use App\Models\Translation;

class TranslationExportControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->create_test_languages();

        $translation_group = TranslationGroup::factory()->create([
            "key" => "auth.login.header",
            "description" => "Login header",
        ]);

        Translation::factory()
            ->forGroup($translation_group)
            ->forLanguage(Language::where("code", "en")->first())
            ->create(["value" => "Welcome Back"]);

        Translation::factory()
            ->forGroup($translation_group)
            ->forLanguage(Language::where("code", "fr")->first())
            ->create(["value" => "Bon retour"]);
    }

    public function test_export_translations_without_authentication()
    {
        $response = $this->getJson("/api/export/translations?language_code=en");

        $response->assertStatus(401)
            ->assertJson([
                "message" => "Unauthenticated.",
            ]);
    }

    public function test_export_translations_with_authentication()
    {
        $auth = $this->create_authenticated_user();

        $response = $this->getJson(
            "/api/export/translations?language_code=en",
            $this->with_authentication_headers($auth["token"])
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                "success",
                "data",
                "meta" => [
                    "language_code",
                    "total_keys",
                    "format",
                    "response_time_ms",
                ],
            ])
            ->assertJson([
                "success" => true,
                "data" => [
                    "auth.login.header" => "Welcome Back",
                ],
            ]);
    }

    public function test_export_translations_with_invalid_language()
    {
        $auth = $this->create_authenticated_user();

        $response = $this->getJson(
            "/api/export/translations?language_code=invalid",
            $this->with_authentication_headers($auth["token"])
        );

        $response->assertStatus(422)
            ->assertJson([
                "message" => "The selected language code is invalid",
            ]);
    }

    public function test_export_translations_with_tags_filter()
    {
        $auth = $this->create_authenticated_user();

        $this->create_test_tags();

        $response = $this->getJson(
            "/api/export/translations?language_code=en&tags[]=web",
            $this->with_authentication_headers($auth["token"])
        );

        $response->assertStatus(200)
            ->assertJson([
                "success" => true,
            ]);
    }
}

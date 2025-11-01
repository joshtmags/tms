<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TranslationService;
use App\Models\Language;
use App\Models\TranslationGroup;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    private TranslationService $translation_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->translation_service = app(TranslationService::class);

        Language::factory()->english()->create();
        Language::factory()->spanish()->create();
        Language::factory()->french()->create();
    }

    public function test_create_translation_group()
    {
        $data = [
            "key" => "test.unique.key",
            "description" => "Test description",
            "translations" => [
                [
                    "language_code" => "en",
                    "value" => "English value",
                ],
                [
                    "language_code" => "fr",
                    "value" => "French value",
                ],
            ],
            "tags" => ["web", "test"],
        ];

        $translation_group = $this->translation_service->createTranslationGroup($data);

        $this->assertInstanceOf(TranslationGroup::class, $translation_group);
        $this->assertEquals("test.unique.key", $translation_group->key);
        $this->assertCount(2, $translation_group->translations);
        $this->assertCount(2, $translation_group->tags);
    }

    public function test_get_translations_with_filters()
    {
        TranslationGroup::factory()
            ->has(Translation::factory()->count(2))
            ->count(5)
            ->create();

        $filters = [
            "per_page" => 10,
            "sort_by" => "key",
            "sort_order" => "asc",
        ];

        $result = $this->translation_service->getTranslations($filters);

        $this->assertCount(5, $result->items());
        $this->assertEquals(5, $result->total());
    }
}

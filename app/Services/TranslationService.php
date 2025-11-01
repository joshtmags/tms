<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationGroup;
use App\Models\TranslationTag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TranslationService
{
    public function __construct(private TranslationExportService $translation_export_service) {}

    public function createTranslationGroup(array $data): TranslationGroup
    {
        return DB::transaction(function () use ($data) {
            $translation_group = TranslationGroup::firstOrCreate(
                ["key" => $data["key"]],
                ["description" => $data["description"] ?? null]
            );

            // Handle translations
            if (isset($data["translations"])) {
                $this->createTranslations($translation_group, $data["translations"]);
            }

            // Handle tags
            if (isset($data["tags"])) {
                $this->syncTags($translation_group, $data["tags"]);
            }

            // Load relationships for resource response
            $translation_group->load(["translations.language", "tags"]);

            $languages = collect($data["translations"])->pluck("language_code");
            foreach ($languages as $language) {
                $this->translation_export_service->clearExportCache($language);
            }

            return $translation_group;
        });
    }

    private function createTranslations(TranslationGroup $translation_group, array $translations): void
    {
        foreach ($translations as $translation_data) {
            $language = Language::where("code", $translation_data["language_code"])->first();

            if ($language) {
                Translation::updateOrCreate(
                    [
                        "translation_group_id" => $translation_group->id,
                        "language_id" => $language->id,
                    ],
                    [
                        "value" => $translation_data["value"],
                    ]
                );
            }
        }
    }

    private function syncTags(TranslationGroup $translation_group, array $tags): void
    {
        $tag_ids = [];

        foreach ($tags as $tag_name) {
            $tag = TranslationTag::firstOrCreate(["name" => Str::slug($tag_name)]);
            $tag_ids[] = $tag->id;
        }

        $translation_group->tags()->sync($tag_ids);
    }

    public function updateTranslationGroup(TranslationGroup $translation_group, array $data): TranslationGroup
    {
        return DB::transaction(function () use ($translation_group, $data) {
            // Update translation group if key changed
            if (isset($data["key"]) && $data["key"] !== $translation_group->key) {
                $translation_group->update(["key" => $data["key"]]);
            }

            if (isset($data["description"])) {
                $translation_group->update(["description" => $data["description"]]);
            }

            // Update translations
            if (isset($data["translations"])) {
                $this->createTranslations($translation_group, $data["translations"]);
            }

            // Update tags
            if (isset($data["tags"])) {
                $this->syncTags($translation_group, $data["tags"]);
            }

            $translation_group->load(["translations.language", "tags"]);

            $languages = collect($data["translations"])->pluck("language_code");
            foreach ($languages as $language) {
                $this->translation_export_service->clearExportCache($language);
            }

            return $translation_group;
        });
    }

    public function getTranslations(array $filters = []): LengthAwarePaginator
    {
        $per_page = $filters["per_page"] ?? 15;
        $sort_by = $filters["sort_by"] ?? "key";
        $sort_order = $filters["sort_order"] ?? "asc";

        $query = TranslationGroup::with(["translations.language", "tags"])
            ->select("translation_groups.*");

        // search by key or description
        if (!empty($filters["search"])) {
            $search_term = "%" . $filters["search"] . "%";
            $query->where(function ($q) use ($search_term) {
                $q->where("translation_groups.key", "LIKE", $search_term)
                    ->orWhere("translation_groups.description", "LIKE", $search_term)
                    ->orWhereHas("translations", function ($q) use ($search_term) {
                        $q->where("value", "LIKE", $search_term);
                    });
            });
        }

        // tags filter
        if (!empty($filters["tags"])) {
            $query->whereHas("tags", function ($q) use ($filters) {
                $q->whereIn("name", $filters["tags"]);
            });
        }

        // language filter
        if (!empty($filters["language"])) {
            $query->whereHas("translations.language", function ($q) use ($filters) {
                $q->where("code", $filters["language"]);
            });
        }

        // sort
        $query->orderBy($sort_by, $sort_order);

        if (!empty($filters["language"])) {
            $query->with([
                "translations" => function ($query) use ($filters) {
                    $query->whereHas("language", function ($q) use ($filters) {
                        $q->where("code", $filters["language"]);
                    });
                },
                "translations.language"
            ]);
        } else {
            $query->with(["translations.language"]);
        }

        return $query->paginate($per_page);
    }

    public function getTranslationById(int $id): ?TranslationGroup
    {
        return TranslationGroup::with(["translations.language", "tags"])
            ->find($id);
    }

    public function getTranslationByKey(string $key): ?TranslationGroup
    {
        return TranslationGroup::with(["translations.language", "tags"])
            ->where("key", $key)
            ->first();
    }

    public function getTranslationsStats(): array
    {
        return [
            "total_groups" => TranslationGroup::count(),
            "total_translations" => Translation::count(),
            "total_languages" => Language::count(),
            "total_tags" => TranslationTag::count(),
            "translations_per_language" => DB::table("translations")
                ->join("languages", "translations.language_id", "=", "languages.id")
                ->select("languages.code", DB::raw("COUNT(*) as count"))
                ->groupBy("languages.id", "languages.code")
                ->get()
                ->pluck("count", "code")
                ->toArray()
        ];
    }
}

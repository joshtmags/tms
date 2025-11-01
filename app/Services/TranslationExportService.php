<?php

namespace App\Services;

use App\Models\Language;
use App\Models\TranslationGroup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TranslationExportService
{
    public function exportTranslations(
        string $language_code,
        array $tags = [],
        string $format = "flat",
        bool $include_empty = false
    ): array {
        $cache_key = $this->generateCacheKey($language_code);

        Log::info("cache key: [{$cache_key}]");

        // try to get from cache if key exists
        return Cache::remember($cache_key, 3600, function () use ($language_code, $tags, $format, $include_empty) {
            return $this->generateExportData($language_code, $tags, $format, $include_empty);
        });
    }

    private function generateExportData(string $language_code, array $tags, string $format, bool $include_empty): array
    {
        $language = Language::where("code", $language_code)->first();
        if (!$language) {
            return [];
        }

        $query = TranslationGroup::with([
            "translations" => function ($query) use ($language) {
                $query->where("language_id", $language->id);
            }
        ])
            ->select("translation_groups.id", "translation_groups.key", "translation_groups.description");

        // Filter by tags if provided
        if (!empty($tags)) {
            $query->whereHas("tags", function ($q) use ($tags) {
                $q->whereIn("name", $tags);
            });
        }

        // Filter out empty translations if required
        if (!$include_empty) {
            $query->whereHas("translations", function ($q) use ($language) {
                $q->where("language_id", $language->id)
                    ->whereNotNull("value")
                    ->where("value", "!=", "");
            });
        }

        $translation_groups = $query->get();

        return $this->formatExportData($translation_groups, $format);
    }

    private function formatExportData($translation_groups, string $format): array
    {
        return match ($format) {
            // "nested" => $this->formatNested($translation_groups),
            // "grouped" => $this->formatGrouped($translation_groups),
            default => $this->formatFlat($translation_groups),
        };
    }

    private function formatFlat($translation_groups): array
    {
        $export_data = [];

        foreach ($translation_groups as $group) {
            $translation = $group->translations->first();
            $export_data[$group->key] = $translation ? $translation->value : "";
        }

        return $export_data;
    }

    private function generateCacheKey(string $language_code): string
    {
        return "translations_export:{$language_code}";
    }

    public function clearExportCache(string $language_code = "all"): void
    {
        Cache::forget($language_code ? "translations_export:{$language_code}" : "translations_export:all");
    }

    /**
     * High-performance method for large datasets using raw SQL
     */
    public function exportTranslationsOptimized(string $language_code, array $tags = [], string $format = "flat"): array
    {
        $language = Language::where("code", $language_code)->first();
        if (!$language) {
            return [];
        }

        $query = DB::table("translation_groups")
            ->select(
                "translation_groups.key",
                "translations.value"
            )
            ->leftJoin("translations", function ($join) use ($language) {
                $join->on("translation_groups.id", "=", "translations.translation_group_id")
                    ->where("translations.language_id", "=", $language->id);
            })
            ->whereNotNull("translations.value")
            ->where("translations.value", "!=", "");

        // Filter by tags if provided
        if (!empty($tags)) {
            $query->join(
                "translation_group_tag",
                "translation_groups.id",
                "=",
                "translation_group_tag.translation_group_id"
            )
                ->join("translation_tags", "translation_group_tag.translation_tag_id", "=", "translation_tags.id")
                ->whereIn("translation_tags.name", $tags)
                ->distinct();
        }

        $results = $query->get();

        return $this->formatExportFromDb($results, $format);
    }

    private function formatExportFromDb($results): array
    {
        $export_data = [];

        foreach ($results as $result) {
            $export_data[$result->key] = $result->value;
        }

        /**
         * @todo handle format here
         */

        return $export_data;
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranslationStatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "total_groups" => $this["total_groups"],
            "total_translations" => $this["total_translations"],
            "total_languages" => $this["total_languages"],
            "total_tags" => $this["total_tags"],
            "translations_per_language" => $this["translations_per_language"],
        ];
    }
}

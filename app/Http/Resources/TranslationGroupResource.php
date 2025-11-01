<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TranslationGroupResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "key" => $this->key,
            "description" => $this->description,
            "translations" => $this->translations->map(function ($translation) {
                return [
                    "id" => $translation->id,
                    "language_code" => $translation->language->code,
                    "language_name" => $translation->language->name,
                    "value" => $translation->value,
                    "updated_at" => $translation->updated_at,
                ];
            }),
            "tags" => $this->tags->pluck("name"),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}

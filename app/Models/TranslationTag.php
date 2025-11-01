<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TranslationTag extends Model
{
    protected $fillable = [
        "name",
    ];

    public function translationGroups(): BelongsToMany
    {
        return $this->belongsToMany(TranslationGroup::class, "translation_group_tag")
            ->withTimestamps();
    }
}

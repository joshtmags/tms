<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        "key",
        "description",
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(TranslationTag::class, "translation_group_tag")
            ->withTimestamps();
    }
}

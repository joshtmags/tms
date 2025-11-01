<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends Model
{
    protected $fillable = [
        "translation_group_id",
        "language_id",
        "value",
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(TranslationGroup::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}

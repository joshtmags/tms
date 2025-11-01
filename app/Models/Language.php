<?php

namespace App\Models;

use App\Enums\LangCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        "code",
        "name",
    ];

    protected function casts(): array
    {
        return [
            // "code" => LangCode::class, // optional enum value casting
        ];
    }

    public function languages(): HasMany
    {
        return $this->hasMany(Language::class);
    }
}

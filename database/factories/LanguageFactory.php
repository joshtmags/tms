<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition()
    {
        return [
            "code" => $this->faker->unique()->languageCode(),
            "name" => $this->faker->word(),
        ];
    }

    public function english()
    {
        return $this->state(function (array $attributes) {
            return [
                "code" => "en",
                "name" => "English",
            ];
        });
    }

    public function french()
    {
        return $this->state(function (array $attributes) {
            return [
                "code" => "fr",
                "name" => "French",
            ];
        });
    }

    public function spanish()
    {
        return $this->state(function (array $attributes) {
            return [
                "code" => "es",
                "name" => "Spanish",
            ];
        });
    }
}

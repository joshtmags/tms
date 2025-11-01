<?php

namespace Database\Factories;

use App\Models\TranslationTag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationTagFactory extends Factory
{
    protected $model = TranslationTag::class;

    public function definition()
    {
        return [
            "name" => $this->faker->unique()->word(),
        ];
    }

    public function mobile()
    {
        return $this->state(function (array $attributes) {
            return [
                "name" => "mobile",
            ];
        });
    }

    public function desktop()
    {
        return $this->state(function (array $attributes) {
            return [
                "name" => "desktop",
            ];
        });
    }

    public function web()
    {
        return $this->state(function (array $attributes) {
            return [
                "name" => "web",
            ];
        });
    }
}

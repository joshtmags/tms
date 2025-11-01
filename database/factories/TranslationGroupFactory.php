<?php

namespace Database\Factories;

use App\Models\TranslationGroup;
use App\Models\TranslationTag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationGroupFactory extends Factory
{
    protected $model = TranslationGroup::class;

    public function definition()
    {
        $categories = ["auth", "validation", "ui", "email", "notification", "common", "error", "success"];
        $sub_categories = [
            "login",
            "register",
            "password",
            "profile",
            "header",
            "button",
            "message",
            "title",
            "label",
            "placeholder",
            "error",
            "success",
            "info",
            "warning"
        ];
        $actions = ["submit", "cancel", "save", "delete", "edit", "create", "update", "confirm"];

        $category = $this->faker->randomElement($categories);
        $sub_category = $this->faker->randomElement($sub_categories);
        $action = $this->faker->randomElement($actions);
        $random_word = $this->faker->word();

        // Generate realistic translation keys
        $key_pattern = $this->faker->randomElement([
            "{$category}.{$sub_category}.{$action}",
            "{$category}.{$sub_category}.{$random_word}",
            "{$category}.{$action}",
            "{$sub_category}.{$action}",
            "common.{$random_word}",
            "ui.{$sub_category}.{$random_word}",
        ]);

        return [
            "key" => $key_pattern . "." . $this->faker->unique()->numberBetween(1000, 1000000),
            "description" => $this->faker->sentence(6),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (TranslationGroup $translation_group) {
            // Attach random tags (1-3 tags per group)
            $tags = TranslationTag::inRandomOrder()
                ->take($this->faker->numberBetween(1, 3))
                ->get();

            $translation_group->tags()->attach($tags);
        });
    }
}

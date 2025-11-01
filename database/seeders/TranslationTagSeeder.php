<?php

namespace Database\Seeders;

use App\Models\TranslationTag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TranslationTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = ["mobile", "desktop", "web"];

        foreach ($tags as $tag) {
            TranslationTag::create(["name" => $tag]);
        }
    }
}

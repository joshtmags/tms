<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $payload = [
            ["code" => "en", "name" => "English", "created_at" => now(), "updated_at" => now()],
            ["code" => "fr", "name" => "French", "created_at" => now(), "updated_at" => now()],
            ["code" => "es", "name" => "Spanish", "created_at" => now(), "updated_at" => now()],
            ["code" => "de", "name" => "German", "created_at" => now(), "updated_at" => now()],
        ];

        Language::insert($payload);
    }
}

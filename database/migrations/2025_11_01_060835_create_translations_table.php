<?php

use App\Models\Language;
use App\Models\TranslationGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("translations", function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TranslationGroup::class, "translation_group_id")->constrained()->onDelete("cascade");
            $table->foreignIdFor(Language::class, "language_id")->constrained()->onDelete("cascade");
            $table->text("value");
            $table->timestamps();

            $table->unique(["translation_group_id", "language_id"]);
            $table->index(["language_id", "translation_group_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("translations");
    }
};

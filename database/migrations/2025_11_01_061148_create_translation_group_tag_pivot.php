<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("translation_group_tag", function (Blueprint $table) {
            $table->id();
            $table->foreignId("translation_group_id")->constrained()->onDelete("cascade");
            $table->foreignId("translation_tag_id")->constrained()->onDelete("cascade");
            $table->timestamps();

            $table->unique(["translation_group_id", "translation_tag_id"], "trans_grp_tag");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("translation_group_tag");
    }
};

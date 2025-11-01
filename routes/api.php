<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TranslationController;
use App\Http\Controllers\API\TranslationExportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix("auth")->group(function () {
    Route::post("/login", [AuthController::class, "login"]);
});

Route::middleware([
    // "throttle:api",
    "auth:sanctum",
])
    ->group(function () {
        Route::prefix("translations")->group(function () {
            Route::get("/", [TranslationController::class, "index"]);
            Route::get("/stats", [TranslationController::class, "stats"]);
            Route::get("/key/{key}", [TranslationController::class, "showByKey"]);
            Route::get("/{id}", [TranslationController::class, "show"]);

            Route::post("/", [TranslationController::class, "store"]);
            Route::put("/{id}", [TranslationController::class, "update"]);
        });

        Route::prefix("export")->group(function () {
            Route::get("/translations", [TranslationExportController::class, "export"]);
            Route::get("/translations/download", [TranslationExportController::class, "download"]);
        });
    });

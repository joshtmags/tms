<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\ViewTranslationRequest;
use App\Http\Resources\TranslationGroupCollection;
use App\Http\Resources\TranslationGroupResource;
use App\Http\Resources\TranslationStatsResource;
use App\Models\TranslationGroup;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TranslationController extends Controller
{
    public function __construct(private TranslationService $translation_service) {}

    public function index(ViewTranslationRequest $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $translations = $this->translation_service->getTranslations($request->validated());

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                "success" => true,
                "data" => new TranslationGroupCollection($translations),
                "response_time_ms" => $responseTime,
                "filters" => $request->validated(),
            ]);
        } catch (\Exception $e) {
            Log::error("Fetching translations failed: " . $e->getMessage());

            return response()->json([
                "success" => false,
                "message" => "Failed to fetch translations",
                "error" => config("app.debug") ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get specific translation by ID
     */
    public function show($id): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $translationGroup = $this->translation_service->getTranslationById($id);

            if (!$translationGroup) {
                return response()->json([
                    "success" => false,
                    "message" => "Translation not found",
                ], 404);
            }

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                "success" => true,
                "data" => new TranslationGroupResource($translationGroup),
                "response_time_ms" => $responseTime,
            ]);
        } catch (\Exception $e) {
            Log::error("Fetching translation {$id} failed: " . $e->getMessage());

            return response()->json([
                "success" => false,
                "message" => "Failed to fetch translation",
            ], 500);
        }
    }

    /**
     * Get translation by key
     */
    public function showByKey(string $key): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $translation_group = $this->translation_service->getTranslationByKey($key);

            if (!$translation_group) {
                return response()->json([
                    "success" => false,
                    "message" => "Translation not found",
                ], 404);
            }

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                "success" => true,
                "data" => new TranslationGroupResource($translation_group),
                "response_time_ms" => $responseTime,
            ]);
        } catch (\Exception $e) {
            Log::error("Fetching translation by key {$key} failed: " . $e->getMessage());

            return response()->json([
                "success" => false,
                "message" => "Failed to fetch translation",
            ], 500);
        }
    }

    /**
     * Get translation statistics
     */
    public function stats(): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $stats = $this->translation_service->getTranslationsStats();

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                "success" => true,
                "data" => new TranslationStatsResource($stats),
                "response_time_ms" => $responseTime,
            ]);
        } catch (\Exception $e) {
            Log::error("Fetching translation stats failed: " . $e->getMessage());

            return response()->json([
                "success" => false,
                "message" => "Failed to fetch translation statistics",
            ], 500);
        }
    }

    public function store(StoreTranslationRequest $request): JsonResponse
    {
        $start_time = microtime(true);

        try {
            $translation_group = $this->translation_service->createTranslationGroup($request->validated());

            $response_time = round((microtime(true) - $start_time) * 1000, 2);

            return response()->json([
                "success" => true,
                "data" => new TranslationGroupResource($translation_group),
                "response_time_ms" => $response_time,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Translation creation failed: " . $e->getMessage());

            return response()->json([
                "success" => false,
                "message" => "Failed to create translation",
                "error" => config("app.debug") ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function update(StoreTranslationRequest $request, int $id): JsonResponse
    {
        $startTime = microtime(true);

        try {
            // Find the translation group manually to handle not found case
            $translation = TranslationGroup::find($id);

            if (!$translation) {
                return response()->json([
                    "success" => false,
                    "message" => "Translation not found",
                    "error" => "No translation found with ID: {$id}"
                ], 404);
            }

            $translation_group = $this->translation_service->updateTranslationGroup(
                $translation,
                $request->validated()
            );

            $response_time = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                "success" => true,
                "data" => new TranslationGroupResource($translation_group),
                "response_time_ms" => $response_time,
            ]);
        } catch (\Exception $e) {
            Log::error("Translation update failed: " . $e->getMessage());

            return response()->json([
                "success" => false,
                "message" => "Failed to update translation",
                "error" => config("app.debug") ? $e->getMessage() : null,
            ], 500);
        }
    }
}

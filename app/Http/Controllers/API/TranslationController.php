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

/**
 * @OA\Schema(
 *     schema="Translation",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="language_code", type="string", example="en"),
 *     @OA\Property(property="language_name", type="string", example="English"),
 *     @OA\Property(property="value", type="string", example="Welcome Back"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="TranslationGroup",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="key", type="string", example="auth.login.header"),
 *     @OA\Property(property="description", type="string", example="Login page header title"),
 *     @OA\Property(
 *         property="translations",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Translation")
 *     ),
 *     @OA\Property(
 *         property="tags",
 *         type="array",
 *         @OA\Items(type="string", example="web")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="TranslationRequest",
 *     type="object",
 *     required={"key", "translations"},
 *     @OA\Property(property="key", type="string", example="auth.login.header"),
 *     @OA\Property(property="description", type="string", example="Login page header title"),
 *     @OA\Property(
 *         property="translations",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"language_code", "value"},
 *             @OA\Property(property="language_code", type="string", example="en"),
 *             @OA\Property(property="value", type="string", example="Welcome Back")
 *         )
 *     ),
 *     @OA\Property(
 *         property="tags",
 *         type="array",
 *         @OA\Items(type="string", example="web")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="TranslationStats",
 *     type="object",
 *     @OA\Property(property="total_groups", type="integer", example=150),
 *     @OA\Property(property="total_translations", type="integer", example=450),
 *     @OA\Property(property="total_languages", type="integer", example=3),
 *     @OA\Property(property="total_tags", type="integer", example=15),
 *     @OA\Property(
 *         property="translations_per_language",
 *         type="object",
 *         @OA\Property(property="en", type="integer", example=150),
 *         @OA\Property(property="fr", type="integer", example=150),
 *         @OA\Property(property="es", type="integer", example=150)
 *     )
 * )
 */
class TranslationController extends Controller
{
    public function __construct(private TranslationService $translation_service) {}

    /**
     * @OA\Get(
     *     path="/api/translations",
     *     summary="List translations",
     *     description="Get paginated list of translations with filtering and search",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in key, description, or translation value",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="tags",
     *         in="query",
     *         description="Filter by tags",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string")
     *         ),
     *         style="form",
     *         explode=true
     *     ),
     *     @OA\Parameter(
     *         name="language_code",
     *         in="query",
     *         description="Filter by language code",
     *         required=false,
     *         @OA\Schema(type="string", example="en")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort field",
     *         required=false,
     *         @OA\Schema(type="string", enum={"key", "created_at", "updated_at"}, default="key")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translations retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/TranslationGroup"),
     *             @OA\Property(property="response_time_ms", type="number", format="float", example=45.23)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
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
     * @OA\Get(
     *     path="/api/translations/{id}",
     *     summary="Get translation by ID",
     *     description="Get a specific translation group by its ID",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Translation group ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/TranslationGroup"),
     *             @OA\Property(property="response_time_ms", type="number", format="float", example=12.45)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Translation not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
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

    /**
     * @OA\Post(
     *     path="/api/translations",
     *     summary="Create translation",
     *     description="Create a new translation group with multiple language translations",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TranslationRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Translation created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/TranslationGroup"),
     *             @OA\Property(property="response_time_ms", type="number", format="float", example=52.18)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
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

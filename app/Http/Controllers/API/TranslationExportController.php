<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExportTranslationRequest;
use App\Services\TranslationExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

/**
 * @OA\Schema(
 *     schema="ExportResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="data", type="object", additionalProperties=true),
 *     @OA\Property(
 *         property="meta",
 *         type="object",
 *         @OA\Property(property="language_code", type="string", example="en"),
 *         @OA\Property(property="total_keys", type="integer", example=150),
 *         @OA\Property(property="format", type="string", example="nested"),
 *         @OA\Property(property="response_time_ms", type="number", format="float", example=45.23)
 *     )
 * )
 */
class TranslationExportController extends Controller
{
    public function __construct(private TranslationExportService $translation_export_service) {}

    /**
     * @OA\Get(
     *     path="/api/export/translations",
     *     summary="Export translations",
     *     description="Export translations in various formats for frontend applications",
     *     tags={"Export"},
     *     @OA\Parameter(
     *         name="language_code",
     *         in="query",
     *         description="Language code for translations",
     *         required=true,
     *         @OA\Schema(type="string", example="en")
     *     ),
     *     @OA\Parameter(
     *         name="format",
     *         in="query",
     *         description="Export format",
     *         required=false,
     *         @OA\Schema(type="string", enum={"flat", "nested", "grouped"}, default="flat")
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
     *         name="include_empty",
     *         in="query",
     *         description="Include empty translations",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translations exported successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ExportResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function export(ExportTranslationRequest $request): JsonResponse
    {
        $start_time = microtime(true);

        try {
            $validated_data = $request->validated();

            $export_data = $this->translation_export_service->exportTranslations(
                $validated_data['language_code'],
                $validated_data['tags'] ?? [],
                $validated_data['format'] ?? 'flat',
                $validated_data['include_empty'] ?? false
            );

            $response_time = round((microtime(true) - $start_time) * 1000, 2);

            return response()->json([
                'success' => true,
                'data' => $export_data,
                'meta' => [
                    'language_code' => $validated_data['language_code'],
                    'total_keys' => count($export_data),
                    'format' => $validated_data['format'] ?? 'flat',
                    'response_time_ms' => $response_time,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Translation export failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to export translations',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/export/translations/download",
     *     summary="Download translations as JSON file",
     *     description="Download translations as a JSON file for direct use in applications",
     *     tags={"Export"},
     *     @OA\Parameter(
     *         name="language_code",
     *         in="query",
     *         description="Language code for translations",
     *         required=true,
     *         @OA\Schema(type="string", example="en")
     *     ),
     *     @OA\Parameter(
     *         name="format",
     *         in="query",
     *         description="Export format",
     *         required=false,
     *         @OA\Schema(type="string", enum={"flat", "nested", "grouped"}, default="flat")
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
     *     @OA\Response(
     *         response=200,
     *         description="File download",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function download(ExportTranslationRequest $request)
    {
        $start_time = microtime(true);

        try {
            $validated_data = $request->validated();

            $export_data = $this->translation_export_service->exportTranslationsOptimized(
                $validated_data['language_code'],
                $validated_data['tags'] ?? [],
                $validated_data['format'] ?? 'flat',
                $validated_data['include_empty'] ?? false
            );

            $file_name = "translations_{$validated_data['language_code']}_" . date('Y-m-d') . '.json';

            return Response::json($export_data)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="' . $file_name . '"')
                ->header('X-Response-Time', round((microtime(true) - $start_time) * 1000, 2) . 'ms');
        } catch (\Exception $e) {
            Log::error('Translation download failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to download translations',
            ], 500);
        }
    }
}

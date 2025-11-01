<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExportTranslationRequest;
use App\Services\TranslationExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class TranslationExportController extends Controller
{
    public function __construct(private TranslationExportService $translation_export_service) {}

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
     * Direct JSON file download for frontend applications
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

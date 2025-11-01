<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="Translation Management Service API",
 *     version="1.0.0",
 *     description="A comprehensive API for managing translations with multi-language support, tagging, and export capabilities.",
 *     @OA\Contact(
 *         email="support@tms.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="User login"
 * )
 * @OA\Tag(
 *     name="Translations",
 *     description="CRUD operations for translation management"
 * )
 * @OA\Tag(
 *     name="Export",
 *     description="Translation export endpoints for frontend applications"
 * )
 */
class DocumentationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/",
     *     summary="API Status",
     *     description="Check if the API is running",
     *     tags={"General"},
     *     @OA\Response(
     *         response=200,
     *         description="API is running",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Translation Management Service API is running"),
     *             @OA\Property(property="version", type="string", example="1.0.0"),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Translation Management Service API is running',
            'version' => '1.0.0',
            'timestamp' => now()->toISOString(),
        ]);
    }
}

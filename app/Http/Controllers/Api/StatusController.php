<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GitHubReadmeStats\GitHubStatsService;
use Illuminate\Http\JsonResponse;
use Throwable;

class StatusController extends Controller
{
    public function up(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function patInfo(GitHubStatsService $statsService): JsonResponse
    {
        try {
            $rate = $statsService->fetchRateLimit();

            return response()->json([
                'configured' => filled(config('github-readme-stats.github.token')),
                'limit' => $rate,
            ]);
        } catch (Throwable) {
            return response()->json([
                'configured' => filled(config('github-readme-stats.github.token')),
                'error' => 'Unable to fetch token rate limit information.',
            ], 502);
        }
    }
}

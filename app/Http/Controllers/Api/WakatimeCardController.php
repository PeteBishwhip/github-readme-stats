<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WakatimeRequest;
use App\Services\GitHubReadmeStats\GitHubStatsService;
use App\Services\GitHubReadmeStats\SvgCardFactory;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class WakatimeCardController extends Controller
{
    public function __invoke(WakatimeRequest $request, GitHubStatsService $statsService, SvgCardFactory $svg): Response
    {
        try {
            $data = $statsService->fetchWakatime($request->validated('username'));

            return response($svg->wakatime($data), 200)->header('Content-Type', 'image/svg+xml');
        } catch (Throwable) {
            return response($svg->error('Unable to fetch WakaTime data right now.'), 502)
                ->header('Content-Type', 'image/svg+xml');
        }
    }
}

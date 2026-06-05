<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StatsRequest;
use App\Services\GitHubReadmeStats\GitHubStatsService;
use App\Services\GitHubReadmeStats\SvgCardFactory;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class StatsCardController extends Controller
{
    public function __invoke(StatsRequest $request, GitHubStatsService $statsService, SvgCardFactory $svg): Response
    {
        try {
            $user = $statsService->fetchUser($request->validated('username'));

            return response($svg->stats($user), 200)->header('Content-Type', 'image/svg+xml');
        } catch (Throwable) {
            return response($svg->error('Unable to fetch GitHub stats right now.'), 502)
                ->header('Content-Type', 'image/svg+xml');
        }
    }
}

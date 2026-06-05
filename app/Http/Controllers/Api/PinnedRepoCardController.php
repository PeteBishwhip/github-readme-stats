<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PinnedRepoRequest;
use App\Services\GitHubReadmeStats\GitHubStatsService;
use App\Services\GitHubReadmeStats\SvgCardFactory;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PinnedRepoCardController extends Controller
{
    public function __invoke(PinnedRepoRequest $request, GitHubStatsService $statsService, SvgCardFactory $svg): Response
    {
        try {
            $data = $request->validated();
            $repo = $statsService->fetchRepository($data['username'], $data['repo']);

            return response($svg->pinnedRepo($repo), 200)->header('Content-Type', 'image/svg+xml');
        } catch (Throwable) {
            return response($svg->error('Unable to fetch repository data right now.'), 502)
                ->header('Content-Type', 'image/svg+xml');
        }
    }
}

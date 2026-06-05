<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GistRequest;
use App\Services\GitHubReadmeStats\GitHubStatsService;
use App\Services\GitHubReadmeStats\SvgCardFactory;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class GistCardController extends Controller
{
    public function __invoke(GistRequest $request, GitHubStatsService $statsService, SvgCardFactory $svg): Response
    {
        try {
            $data = $request->validated();
            $gist = $statsService->fetchGist($data['username'], $data['id']);

            return response($svg->gist($gist), 200)->header('Content-Type', 'image/svg+xml');
        } catch (Throwable) {
            return response($svg->error('Unable to fetch gist data right now.'), 502)
                ->header('Content-Type', 'image/svg+xml');
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopLanguagesRequest;
use App\Services\GitHubReadmeStats\GitHubStatsService;
use App\Services\GitHubReadmeStats\SvgCardFactory;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TopLanguagesCardController extends Controller
{
    public function __invoke(TopLanguagesRequest $request, GitHubStatsService $statsService, SvgCardFactory $svg): Response
    {
        try {
            $data = $request->validated();
            $languages = $statsService->fetchTopLanguages($data['username'], (int) ($data['limit'] ?? 5));

            return response($svg->topLanguages($languages), 200)->header('Content-Type', 'image/svg+xml');
        } catch (Throwable) {
            return response($svg->error('Unable to fetch top languages right now.'), 502)
                ->header('Content-Type', 'image/svg+xml');
        }
    }
}

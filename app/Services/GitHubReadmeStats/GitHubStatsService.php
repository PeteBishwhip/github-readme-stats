<?php

namespace App\Services\GitHubReadmeStats;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GitHubStatsService
{
    public function fetchUser(string $username): array
    {
        return $this->githubRequest()
            ->get('/users/'.$username)
            ->throw()
            ->json();
    }

    public function fetchTopLanguages(string $username, int $limit = 5): array
    {
        $repos = $this->githubRequest()
            ->get('/users/'.$username.'/repos', [
                'type' => 'owner',
                'sort' => 'updated',
                'per_page' => 100,
            ])
            ->throw()
            ->json();

        $languages = collect($repos)
            ->reduce(function (array $carry, array $repo): array {
                $language = Arr::get($repo, 'language');
                if ($language === null) {
                    return $carry;
                }

                $carry[$language] = ($carry[$language] ?? 0) + (int) Arr::get($repo, 'size', 0);

                return $carry;
            }, []);

        return collect($languages)
            ->sortDesc()
            ->take($limit)
            ->all();
    }

    public function fetchRepository(string $username, string $repository): array
    {
        return $this->githubRequest()
            ->get('/repos/'.$username.'/'.$repository)
            ->throw()
            ->json();
    }

    public function fetchGist(string $username, string $gistId): array
    {
        $gist = $this->githubRequest()
            ->baseUrl('https://api.github.com')
            ->get('/gists/'.$gistId)
            ->throw()
            ->json();

        $owner = Arr::get($gist, 'owner.login');

        if ($owner === null || strcasecmp($owner, $username) !== 0) {
            throw new RuntimeException('The gist does not belong to the requested user.');
        }

        return $gist;
    }

    public function fetchWakatime(string $username): array
    {
        $apiKey = config('github-readme-stats.wakatime.api_key');

        if ($apiKey === null || $apiKey === '') {
            throw new RuntimeException('WakaTime API key is not configured.');
        }

        return Http::baseUrl(config('github-readme-stats.wakatime.base_url'))
            ->acceptJson()
            ->connectTimeout(config('github-readme-stats.http.connect_timeout'))
            ->timeout(config('github-readme-stats.http.timeout'))
            ->retry([100, 300, 800], throw: false)
            ->withToken($apiKey)
            ->get('/users/'.$username.'/stats/last_7_days')
            ->throw()
            ->json('data', []);
    }

    public function fetchRateLimit(): array
    {
        return $this->githubRequest()
            ->get('/rate_limit')
            ->throw()
            ->json('rate', []);
    }

    private function githubRequest(): PendingRequest
    {
        return Http::baseUrl(config('github-readme-stats.github.base_url'))
            ->acceptJson()
            ->connectTimeout(config('github-readme-stats.http.connect_timeout'))
            ->timeout(config('github-readme-stats.http.timeout'))
            ->retry([100, 300, 800], throw: false)
            ->when(
                filled(config('github-readme-stats.github.token')),
                fn (PendingRequest $request): PendingRequest => $request->withToken(config('github-readme-stats.github.token')),
            )
            ->withHeaders([
                'X-GitHub-Api-Version' => '2022-11-28',
            ]);
    }
}

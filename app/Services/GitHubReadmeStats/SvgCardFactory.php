<?php

namespace App\Services\GitHubReadmeStats;

use Illuminate\Support\Arr;

class SvgCardFactory
{
    public function stats(array $user): string
    {
        return $this->card('GitHub Stats', [
            'User' => Arr::get($user, 'login', 'unknown'),
            'Public Repos' => (string) Arr::get($user, 'public_repos', 0),
            'Followers' => (string) Arr::get($user, 'followers', 0),
            'Following' => (string) Arr::get($user, 'following', 0),
            'Public Gists' => (string) Arr::get($user, 'public_gists', 0),
        ]);
    }

    public function topLanguages(array $languages): string
    {
        $rows = collect($languages)
            ->sortDesc()
            ->take(10)
            ->mapWithKeys(fn (int $bytes, string $language): array => [$language => number_format($bytes).' bytes'])
            ->all();

        return $this->card('Top Languages', $rows === [] ? ['Info' => 'No language data found'] : $rows);
    }

    public function pinnedRepo(array $repo): string
    {
        return $this->card('Pinned Repository', [
            'Name' => Arr::get($repo, 'full_name', 'unknown'),
            'Description' => Arr::get($repo, 'description', 'N/A') ?: 'N/A',
            'Language' => Arr::get($repo, 'language', 'N/A') ?: 'N/A',
            'Stars' => (string) Arr::get($repo, 'stargazers_count', 0),
            'Forks' => (string) Arr::get($repo, 'forks_count', 0),
        ]);
    }

    public function gist(array $gist): string
    {
        return $this->card('Gist', [
            'ID' => Arr::get($gist, 'id', 'unknown'),
            'Description' => Arr::get($gist, 'description', 'N/A') ?: 'N/A',
            'Files' => (string) count(Arr::get($gist, 'files', [])),
            'Comments' => (string) Arr::get($gist, 'comments', 0),
        ]);
    }

    public function wakatime(array $data): string
    {
        return $this->card('WakaTime', [
            'Range' => Arr::get($data, 'range', 'last_7_days'),
            'Total' => Arr::get($data, 'human_readable_total_including_other_language', 'N/A'),
            'Daily Average' => Arr::get($data, 'human_readable_daily_average_including_other_language', 'N/A'),
            'Languages' => (string) count(Arr::get($data, 'languages', [])),
        ]);
    }

    public function error(string $message): string
    {
        return $this->card('Error', ['Message' => $message]);
    }

    private function card(string $title, array $rows): string
    {
        $safeRows = collect($rows)
            ->map(fn ($value, $key): string => sprintf('%s: %s', $this->escape((string) $key), $this->escape((string) $value)))
            ->values();

        $height = max(140, 60 + ($safeRows->count() * 18));

        $lines = $safeRows
            ->map(fn (string $line, int $index): string => '<text x="20" y="'.(70 + ($index * 18)).'" font-family="Verdana" font-size="12" fill="#c9d1d9">'.$line.'</text>')
            ->implode("\n");

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="495" height="{$height}" role="img" aria-label="{$this->escape($title)}">
  <rect x="0" y="0" width="495" height="{$height}" fill="#0d1117" rx="8" />
  <text x="20" y="38" font-family="Verdana" font-size="20" fill="#58a6ff">{$this->escape($title)}</text>
  {$lines}
</svg>
SVG;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

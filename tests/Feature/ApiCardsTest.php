<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApiCardsTest extends TestCase
{
    public function test_stats_endpoint_returns_svg(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.github.com/users/test-user' => Http::response([
                'login' => 'test-user',
                'public_repos' => 12,
                'followers' => 9,
                'following' => 4,
                'public_gists' => 2,
            ]),
        ]);

        $this->get('/api?username=test-user')
            ->assertOk()
            ->assertHeader('content-type', 'image/svg+xml')
            ->assertSee('GitHub Stats', false)
            ->assertSee('test-user', false);
    }

    public function test_top_languages_endpoint_returns_svg(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.github.com/users/test-user/repos*' => Http::response([
                ['language' => 'PHP', 'size' => 120],
                ['language' => 'PHP', 'size' => 80],
                ['language' => 'JavaScript', 'size' => 100],
            ]),
        ]);

        $this->get('/api/top-langs?username=test-user&limit=2')
            ->assertOk()
            ->assertSee('Top Languages', false)
            ->assertSee('PHP', false);
    }

    public function test_pin_endpoint_returns_svg(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.github.com/repos/test-user/test-repo' => Http::response([
                'full_name' => 'test-user/test-repo',
                'description' => 'Sample repo',
                'language' => 'PHP',
                'stargazers_count' => 42,
                'forks_count' => 7,
            ]),
        ]);

        $this->get('/api/pin?username=test-user&repo=test-repo')
            ->assertOk()
            ->assertSee('Pinned Repository', false)
            ->assertSee('test-user/test-repo', false);
    }

    public function test_status_up_endpoint_returns_health_json(): void
    {
        $this->getJson('/api/status/up')
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonStructure(['timestamp']);
    }

    public function test_missing_username_returns_validation_error(): void
    {
        $this->getJson('/api')
            ->assertUnprocessable();
    }
}

# GitHub Readme Stats (Laravel 13)

This repository has been reworked into a Laravel 13 application that serves SVG cards for GitHub profile data.

## Endpoints

- `GET /api?username={username}`: user stats card
- `GET /api/top-langs?username={username}&limit=5`: top languages card
- `GET /api/pin?username={username}&repo={repo}`: pinned repository card
- `GET /api/gist?username={username}&id={gist_id}`: gist card
- `GET /api/wakatime?username={username}`: WakaTime card
- `GET /api/status/up`: health status JSON
- `GET /api/status/pat-info`: GitHub token/rate-limit info JSON

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

## Configuration

Configure these values in `.env`:

- `GITHUB_TOKEN` (optional but recommended to reduce GitHub API rate limiting)
- `WAKATIME_API_KEY` (required for `/api/wakatime`)
- `READMESTATS_CONNECT_TIMEOUT`
- `READMESTATS_TIMEOUT`

## Testing

```bash
composer run test
```

## Notes

- Responses for card endpoints are SVG (`image/svg+xml`).
- API routes are throttled (`60 requests/minute`).
- The app uses Laravel HTTP client retries/timeouts for external API calls.

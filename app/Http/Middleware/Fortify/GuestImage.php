<?php

declare(strict_types=1);

namespace App\Http\Middleware\Fortify;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class GuestImage
{
    private string $baseUrl = 'https://static.jules.nl/img/blueprint/guest/';

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $results = $this->fetchPhotosList();
        $result = null;

        if ($results->isNotEmpty()) {
            // different photo every hour
            $index = crc32(config('app.key') . ':' . date('YmjH')) % $results->count();
            $result = $results->get($index);
        }

        if (empty($result)) {
            return $next($request);
        }

        $result['img'] = vsprintf('%s%s', [
            $this->baseUrl,
            Arr::pull($result, 'basename'),
        ]);

        Inertia::share('app.guestImage', $result);

        return $next($request);
    }

    private function fetchPhotosList()
    {
        $key = hash('xxh3', __METHOD__);

        $results = Cache::flexible($key, ['+1day', '+15 days'], function () {
            $response = Http::timeout(5)
                ->baseUrl($this->baseUrl)
                ->get('photos.json');

            if (! $response->successful()) {
                return [];
            }

            return $response->json();
        });

        return collect($results);
    }
}

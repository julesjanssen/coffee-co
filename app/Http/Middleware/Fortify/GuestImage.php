<?php

declare(strict_types=1);

namespace App\Http\Middleware\Fortify;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class GuestImage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
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
            return;
        }

        View::share('guestImage', $result);

        return $next($request);
    }

    private function fetchPhotosList()
    {
        $key = hash('xxh3', __METHOD__);

        $results = Cache::flexible($key, ['+1day', '+15 days'], function () {
            $response = Http::get('https://static.jules.nl/img/blueprint/guest/photos.json');
            if (! $response->successful()) {
                return [];
            }

            return $response->json();
        });

        return collect($results);
    }
}

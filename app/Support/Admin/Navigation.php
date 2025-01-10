<?php

declare(strict_types=1);

namespace App\Support\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Yaml\Yaml;

class Navigation
{
    private function __construct(
        private readonly Request $request,
    ) {}

    public static function build(Request $request)
    {
        return (new self($request))->generate();
    }

    protected function generate()
    {
        return collect($this->getNavigationConfig())
            ->map(function ($group) {
                $items = Arr::get($group, 'items', []);
                $items = $this->processItems(collect($items));

                if (! $items->count()) {
                    return;
                }

                return (object) [
                    'title' => $group['title'],
                    'items' => $items,
                ];
            })
            ->filter();
    }

    private function processItems(Collection $items)
    {
        $user = $this->request->user();
        if (empty($user)) {
            return collect([]);
        }

        return $items
            ->map(function ($item) use ($user) {
                $permission = Arr::get($item, 'permission');
                if (! empty($permission) && ! $user->can($permission)) {
                    return;
                }

                return new NavigationItem($item, $this->request);
            })
            ->filter();
    }

    private function getNavigationConfig()
    {
        return Cache::rememberForever(__METHOD__, function () {
            $path = resource_path('config/admin/navigation.yaml');

            return Yaml::parseFile($path);
        });
    }
}

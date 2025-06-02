<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Resources\Admin\ChangelogVersionResource;
use App\Support\Changelog\ChangelogParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ChangelogController
{
    public function index(Request $request)
    {
        Gate::authorize('admin.system.changelog.view');

        return Inertia::render('system/changelog', [
            'releases' => fn() => ChangelogVersionResource::collection($this->listReleases()),
        ]);
    }

    public function latest(Request $request)
    {
        Gate::authorize('admin.system.changelog.view');

        $latest = $this->listReleases()
            ->filter(fn($v) => ! $v->isUnreleased)
            ->first();

        return ChangelogVersionResource::make($latest);
    }

    private function listReleases()
    {
        return new ChangelogParser(base_path('CHANGELOG.md'))
            ->parse()
            ->when(
                ! App::isLocal(),
                fn($v) => $v->filter(fn($v) => ! $v->isUnreleased),
            )
            ->values()
            ->take(10);
    }
}

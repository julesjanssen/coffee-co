<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Support\Admin\Server\Configuration;
use App\Support\Admin\Server\Health;
use App\Support\Admin\Server\OsInternet;
use App\Support\Admin\Server\Php;
use App\Support\Admin\Server\Storage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ServerController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('system/server', [
            'configuration' => fn() => (new Configuration())->toArray(),
            'health' => fn() => (new Health())->toArray(),
            'osInternet' => Inertia::defer(fn() => (new OsInternet())->toArray()),
            'php' => Inertia::defer(fn() => (new Php())->toArray()),
            'storage' => Inertia::defer(fn() => (new Storage())->toArray()),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'action' => ['required', 'string', Rule::in(['resetOpcache'])],
        ]);

        return match ($request->input('action')) {
            'resetOpcache' => $this->resetOpcache(),
            default => new BadRequestException(),
        };
    }

    private function resetOpcache()
    {
        if (opcache_reset()) {
            return response()->noContent();
        }
    }
}

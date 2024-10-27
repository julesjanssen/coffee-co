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
use Inertia\Inertia;

class ServerController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('system/server', [
            'configuration' => fn() => (new Configuration())->toArray(),
            'health' => fn() => (new Health())->toArray(),
            'osInternet' => fn() => (new OsInternet())->toArray(),
            'php' => fn() => (new Php())->toArray(),
            'storage' => fn() => (new Storage())->toArray(),
        ]);
    }
}

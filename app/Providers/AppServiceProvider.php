<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Support\Multitenancy\DatabaseSessionManager;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider as BaseTelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (class_exists(BaseTelescopeServiceProvider::class)) {
            $this->app->register(TelescopeServiceProvider::class);
            $this->app->register(BaseTelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDate();
        $this->configureEloquent();
        $this->configureSessionHandler();
        $this->configureJsonResources();
        $this->configureEnvPath();
        $this->configureHttpClientUserAgent();
    }

    private function configureDate()
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureEloquent()
    {
        Relation::enforceMorphMap([
            'user' => User::class,
        ]);

        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventAccessingMissingAttributes();
    }

    private function configureSessionHandler()
    {
        $this->app['session']->extend('database', function (Application $app) {
            $connection = $app['db']->connection(config('session.connection'));
            $table = config('session.table', 'sessions');
            $minutes = config('session.lifetime');

            return new DatabaseSessionManager(
                $connection,
                $table,
                $minutes,
                $app
            );
        });
    }

    private function configureJsonResources()
    {
        JsonResource::withoutWrapping();
    }

    private function configureEnvPath()
    {
        \putenv('PATH=' . config('blauwdruk.path'));
    }

    private function configureHttpClientUserAgent()
    {
        $userAgent = vsprintf('Mozilla/5.0 (KHTML, like Gecko) (compatible; %s; +%s)', [
            config('app.title'),
            config('app.url'),
        ]);

        Http::globalRequestMiddleware(fn($request) => $request->withHeader('User-Agent', $userAgent));
    }
}

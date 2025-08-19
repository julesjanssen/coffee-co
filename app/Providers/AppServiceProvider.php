<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\GameCampaignCode;
use App\Models\GameFacilitator;
use App\Models\GameParticipant;
use App\Models\GameSession;
use App\Models\Project;
use App\Models\ProjectAction;
use App\Models\ScenarioClient;
use App\Models\User;
use App\Support\Multitenancy\DatabaseSessionManager;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Telescope\TelescopeServiceProvider as BaseTelescopeServiceProvider;
use RuntimeException;

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
        $this->configureAuthenticationRedirects();
        $this->configureSessionHandler();
        $this->configureJsonResources();
        $this->configureEnvPath();
        $this->configureHttpClientUserAgent();
        $this->configureRequestParticipantMacro();
        $this->configureRequestFacilitatorMacro();
    }

    private function configureDate()
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureEloquent()
    {
        Relation::enforceMorphMap([
            'game-campaign-code' => GameCampaignCode::class,
            'game-facilitator' => GameFacilitator::class,
            'game-participant' => GameParticipant::class,
            'game-session' => GameSession::class,
            'project' => Project::class,
            'project-action' => ProjectAction::class,
            'scenario-client' => ScenarioClient::class,
            'user' => User::class,
        ]);

        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventAccessingMissingAttributes();
    }

    private function configureAuthenticationRedirects()
    {
        Authenticate::redirectUsing(function (Request $request) {
            if (Str::startsWith($request->uri()->path(), 'game')) {
                return '/game/sessions';
            }

            return '/auth/login';
        });
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

    private function configureRequestParticipantMacro()
    {
        Request::macro('participant', function (): GameParticipant {
            /** @var Request $this */
            /** @var User|GameParticipant $user */
            $user = $this->user();
            if ($user instanceof GameParticipant) {
                return $user;
            }

            throw new RuntimeException('Authenticated entity is not a GameParticipant');
        });
    }

    private function configureRequestFacilitatorMacro()
    {
        Request::macro('facilitator', function (): GameFacilitator {
            /** @var Request $this */
            /** @var User|GameFacilitator $user */
            $user = $this->user();
            if ($user instanceof GameFacilitator) {
                return $user;
            }

            throw new RuntimeException('Authenticated entity is not a GameFacilitator');
        });
    }
}

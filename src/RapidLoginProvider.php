<?php

namespace Veltisan\RapidLogin;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Veltisan\RapidLogin\Middleware\InjectRapidLogin;

class RapidLoginProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/rapidlogin.php' => $this->app->configPath('rapidlogin.php'),
        ], 'rapidlogin-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => $this->app->resourcePath('views/vendor/rapidlogin'),
        ], 'rapidlogin-views');

        $this->mergeConfigFrom(__DIR__ . '/../config/rapidlogin.php', 'rapidlogin');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'rapidlogin');

        if ((bool) Config::get('rapidlogin.enabled', false)) {
            $model = Config::get('rapidlogin.user_model');
            $routeKeyName = Config::get('rapidlogin.user_route_key_name', 'id');

            Route::get('_rapidlogin/login/{user}', function (string $user) use ($model, $routeKeyName) {
                $user = $model::query()->where($routeKeyName, $user)->firstOrFail();

                Auth::login($user);
                Session::regenerate();

                return Redirect::route(Config::get('rapidlogin.home_route_name', 'home'));
            })->middleware('web')
                ->name('rapidlogin.login');

            $kernel = $this->app[Kernel::class];

            $kernel->pushMiddleware(InjectRapidLogin::class);
        }
    }
}

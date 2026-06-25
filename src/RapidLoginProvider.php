<?php

namespace Veltisan\RapidLogin;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
            Route::get('_rapidlogin/login/{user}/{guard?}', function (string $user, ?string $guard = null) {
                $guard ??= RapidLogin::defaultGuard();

                if (!in_array($guard, RapidLogin::knownGuards(), true)) {
                    throw new NotFoundHttpException();
                }

                $config = RapidLogin::guardConfig($guard);
                $model = $config['model'];

                $authUser = $model::query()
                    ->where($config['route_key_name'], $user)
                    ->firstOrFail();

                Auth::guard($guard)->login($authUser);
                Session::regenerate();

                return Redirect::route($config['home_route']);
            })->middleware('web')
                ->name('rapidlogin.login');

            $kernel = $this->app[Kernel::class];

            $kernel->pushMiddleware(InjectRapidLogin::class);
        }
    }
}

<?php

namespace Veltisan\RapidLogin;

use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
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
            __DIR__ . '/../config/rapidlogin.php' => config_path('rapidlogin.php'),
        ], 'rapidlogin-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/rapidlogin'),
        ], 'rapidlogin-views');

        $this->mergeConfigFrom(__DIR__ . '/../config/rapidlogin.php', 'rapidlogin');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'rapidlogin');

        if ((bool) config('rapidlogin.enabled', false)) {
            $routeKeyName = config('rapidlogin.user_route_key_name', 'id');

            Route::get("_rapidlogin/login/{user:{$routeKeyName}}", function (User $user) {
                Auth::login($user);
                request()->session()->regenerate();

                return redirect()->route(config('rapidlogin.home_route_name', 'home'));
            })->middleware('web')
                ->name('rapidlogin.login');

            $kernel = $this->app[Kernel::class];

            $kernel->pushMiddleware(InjectRapidLogin::class);
        }
    }
}

<?php

namespace Veltisan\RapidLogin\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Veltisan\RapidLogin\RapidLogin;

class InjectRapidLogin
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        //skip when response is not classic response (for example: StreamedResponse, BinaryFileResponse is skipped)
        if (!$response instanceof Response) {
            return $response;
        }

        //skip when disabled or running tests
        if (!Config::get('rapidlogin.enabled', false) || App::runningUnitTests()) {
            return $response;
        }

        //skip routes that don't match the configured route name patterns
        if (!$request->routeIs(...str(Config::get('rapidlogin.route_name_pattern'))->explode(',')->all())
            || $request->routeIs(...str(Config::get('rapidlogin.route_name_negative_pattern'))->explode(',')->all())) {
            return $response;
        }

        //resolve the guard that applies to this route, and its users
        $guard = RapidLogin::resolveGuard($request);
        $users = $this->resolveUsers(RapidLogin::guardConfig($guard));

        $html = View::make('rapidlogin::links', [
            'users' => $users,
            'guard' => $guard,
            'showCloseButton' => Config::get('rapidlogin.show_close_button', true),
            'links' => Config::get('rapidlogin.links', []),
        ])->render();

        $content = Str::replaceLast('</body>', $html . '</body>', $response->getContent());
        $response->setContent($content);

        return $response;
    }

    /**
     * Resolve the list of [key => label] users to show for a guard.
     * Uses the configured users, or falls back to the first 3 rows of the guard's model.
     */
    protected function resolveUsers(array $guardConfig): iterable
    {
        if (!empty($guardConfig['users'])) {
            return $guardConfig['users'];
        }

        $model = $guardConfig['model'];

        if (empty($model)) {
            return [];
        }

        $userKey = $guardConfig['route_key_name'];

        return $model::query()->limit(3)->get()->mapWithKeys(function ($user) use ($userKey) {
            $value = null;
            $attributes = ['name', 'username', 'email'];

            foreach ($attributes as $attribute) {
                if (!is_null($user->$attribute)) {
                    $value = $user->$attribute;
                    break;
                }
            }

            return [$user->{$userKey} => $value ?? $user->{$userKey}];
        });
    }
}

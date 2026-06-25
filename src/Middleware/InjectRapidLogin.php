<?php

namespace Veltisan\RapidLogin\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

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

        $users = Config::get('rapidlogin.users');
        $userKey = Config::get('rapidlogin.user_route_key_name');

        //if no users are defined, get first 3 users from database
        if (empty($users)) {
            $model = Config::get('rapidlogin.user_model');

            $attributes = ['name', 'username', 'email'];

            /** @var Collection $users */
            $users = $model::query()->limit(3)->get()->mapWithKeys(function ($user) use ($attributes, $userKey) {
                $value = null;
                foreach ($attributes as $attribute) {
                    if (!is_null($user->$attribute)) {
                        $value = $user->$attribute;
                        break;
                    }
                }
                return [$user->{$userKey} => $value ?? $user->{$userKey}];
            });
        }

        if ($request->routeIs(str(Config::get('rapidlogin.route_name_pattern'))->explode(','))
            && !$request->routeIs(str(Config::get('rapidlogin.route_name_negative_pattern'))->explode(','))) {
            $content = $response->getContent();

            $html = View::make('rapidlogin::links', [
                'users' => $users,
                'showCloseButton' => Config::get('rapidlogin.show_close_button', true),
                'links' => Config::get('rapidlogin.links', []),
            ])->render();


            $content = Str::replaceLast('</body>', $html . '</body>', $content);
            $response->setContent($content);
        }

        return $response;
    }
}

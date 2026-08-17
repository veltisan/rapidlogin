<?php

namespace Veltisan\RapidLogin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class RapidLogin
{
    /**
     * The guard rapidlogin falls back to when no guard_routes pattern matches.
     * Defaults to the application's default Laravel guard.
     */
    public static function defaultGuard(): string
    {
        return Config::get('rapidlogin.default_guard')
            ?: Config::get('auth.defaults.guard');
    }

    /**
     * Resolve which guard applies to the current request.
     * The first matching guard_routes route-name pattern wins; otherwise the default guard.
     */
    public static function resolveGuard(Request $request): string
    {
        foreach ((array) Config::get('rapidlogin.guard_routes', []) as $pattern => $guard) {
            if ($request->routeIs(...str($pattern)->explode(',')->all())) {
                return $guard;
            }
        }

        return self::defaultGuard();
    }

    /**
     * Every guard rapidlogin is allowed to authenticate against.
     * Used to reject arbitrary guard names arriving via the login URL.
     *
     * @return list<string>
     */
    public static function knownGuards(): array
    {
        return array_values(array_unique(array_merge(
            [self::defaultGuard()],
            array_values((array) Config::get('rapidlogin.guard_routes', [])),
            array_keys((array) Config::get('rapidlogin.guards', [])),
        )));
    }

    /**
     * Resolve the rapidlogin settings for a specific guard, applying the
     * fallback chain documented in config/rapidlogin.php.
     *
     * @return array{model: class-string|null, users: array, route_key_name: string, home_route: string}
     */
    public static function guardConfig(string $guard): array
    {
        $settings = (array) (((array) Config::get('rapidlogin.guards', []))[$guard] ?? []);
        $isDefault = $guard === self::defaultGuard();

        return [
            'model' => $settings['model']
                ?? ($isDefault ? Config::get('rapidlogin.user_model') : null)
                ?? self::modelForGuard($guard),

            'users' => $settings['users']
                ?? ($isDefault ? Config::get('rapidlogin.users', []) : []),

            'route_key_name' => $settings['route_key_name']
                ?? ($isDefault ? Config::get('rapidlogin.user_route_key_name', 'id') : 'id'),

            'home_route' => $settings['home_route']
                ?? Config::get('rapidlogin.home_route_name', 'home'),
        ];
    }

    /**
     * Derive a guard's model from the app's auth config
     * (auth.guards.{guard}.provider -> auth.providers.{provider}.model).
     *
     * @return class-string|null
     */
    protected static function modelForGuard(string $guard): ?string
    {
        $provider = Config::get("auth.guards.{$guard}.provider");

        return $provider
            ? Config::get("auth.providers.{$provider}.model")
            : null;
    }
}

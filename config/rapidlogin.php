<?php

use App\Models\User;

return [

    'enabled' => env('RAPIDLOGIN_ENABLED', false),

    'show_close_button' => env('RAPIDLOGIN_SHOW_CLOSE_BUTTON', true),

    'home_route_name' => env('RAPIDLOGIN_HOME_ROUTE_NAME', 'home'),

    // Users to show, as id:name pairs, e.g. RAPIDLOGIN_USERS="1:John Doe,1337:Jane Doe".
    // When empty, the first 3 users from the database are used.
    'users' => str(env('RAPIDLOGIN_USERS'))
        ->explode(',')
        ->filter()
        ->mapWithKeys(function ($item) {
            [$id, $name] = explode(':', $item);

            return [$id => $name];
        })
        ->toArray(),

    // Or set users as an array in the published config (key = id, value = button label):
    // 'users' => [
    //    1 => 'admin',
    // ],

    'user_model' => env('RAPIDLOGIN_USER_MODEL', User::class),

    'user_route_key_name' => env('RAPIDLOGIN_USER_ROUTE_KEY_NAME', 'id'),

    // Route names the panel shows on (comma-separated), e.g. 'login', 'login*'. '*' = all routes.
    'route_name_pattern' => env('RAPIDLOGIN_ROUTE_NAME_PATTERN', '*'),
    // Route names to exclude.
    'route_name_negative_pattern' => env('RAPIDLOGIN_ROUTE_NAME_NEGATIVE_PATTERN', ''),

    // Extra links for the panel, as text:url pairs, e.g. RAPIDLOGIN_LINKS="Docs:https://app.test".
    'links' => str(env('RAPIDLOGIN_LINKS'))
        ->explode(',')
        ->filter()
        ->mapWithKeys(function ($item) {
            [$text, $url] = str($item)->explode(':', 2);

            return [$text => $url];
        })
        ->toArray(),

    // ---- Multiple guards ----

    // Guard used when no guard_routes pattern matches. Empty = config('auth.defaults.guard').
    'default_guard' => env('RAPIDLOGIN_DEFAULT_GUARD'),

    // Map route-name patterns to a guard (first match wins), e.g. RAPIDLOGIN_GUARD_ROUTES="admin.*:web".
    'guard_routes' => str(env('RAPIDLOGIN_GUARD_ROUTES'))
        ->explode(',')
        ->filter()
        ->mapWithKeys(function ($item) {
            [$pattern, $guard] = str($item)->explode(':', 2);

            return [$pattern => $guard];
        })
        ->toArray(),

    // Model and users per guard, only needed for non-default guards. Omitted keys fall back:
    // model from config/auth.php, users auto-fetched, route_key_name 'id', home_route from home_route_name.
    'guards' => [
        // 'web' => [
        //     'model'          => App\Models\Admin::class,
        //     'route_key_name' => 'id',
        //     'users'          => [1 => 'super admin'],
        //     'home_route'     => 'admin.dashboard',
        // ],
    ],

];

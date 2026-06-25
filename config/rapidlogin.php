<?php

return [

    'enabled' => env('RAPIDLOGIN_ENABLED', false),

    'show_close_button' => env('RAPIDLOGIN_SHOW_CLOSE_BUTTON', true),

    'home_route_name' => env('RAPIDLOGIN_HOME_ROUTE_NAME', 'home'),

    // This allows for easy user setup via the .env file (e.g., RAPIDLOGIN_USERS="1:John Doe,1337:Jane Doe").
    // When empty, the first 3 users from the database will be used.
    'users' => str(env('RAPIDLOGIN_USERS'))
        ->explode(',')
        ->filter()
        ->mapWithKeys(function ($item) {
            [$id, $name] = explode(':', $item);
            return [$id => $name];
        })
        ->toArray(),

    // If you have published the config file, you may wish to set the users with a simple array.
    // The `key` is the user's `id` in the database, and the `value` is a string displayed in the button (doesn't have to match the database).
    //'users' => [
    //    1 => 'admin',
    //],

    'user_model' => env('RAPIDLOGIN_USER_MODEL', App\Models\User::class),

    // This is used in route model binding.
    'user_route_key_name' => env('RAPIDLOGIN_USER_ROUTE_KEY_NAME', 'id'),

    // Examples: 'login', 'login*', etc. Defaults to '*' to match all routes.
    // Separate multiple route names with a comma.
    'route_name_pattern' => env('RAPIDLOGIN_ROUTE_NAME_PATTERN', '*'),
    // Negative pattern for route names, if a route matches this pattern, it will not be injected.
    'route_name_negative_pattern' => env('RAPIDLOGIN_ROUTE_NAME_NEGATIVE_PATTERN', ''),

    // This enables easy setup of additional links via the .env file (e.g., RAPIDLOGIN_LINKS="Link1:https://app.test,Link2:https://app2.test").
    'links' => str(env('RAPIDLOGIN_LINKS'))
        ->explode(',')
        ->filter()
        ->mapWithKeys(function ($item) {
            [$text, $url] = str($item)->explode(':', 2);
            return [$text => $url];
        })
        ->toArray(),

    // ===================== Multiple guards =====================
    // By default rapidlogin authenticates against your app's default guard, using the
    // `users`/`user_model`/`user_route_key_name` above. The settings below let you log
    // into a different guard depending on which route the panel is shown on.

    // Guard used on routes that match none of the `guard_routes` patterns below.
    // When empty, the application's default guard (config('auth.defaults.guard')) is used.
    'default_guard' => env('RAPIDLOGIN_DEFAULT_GUARD'),

    // Map route-name patterns to a guard. The first matching pattern wins; routes that
    // match nothing fall back to `default_guard`. Patterns use the same syntax as
    // `route_name_pattern` (e.g. 'admin.*'). The first match wins.
    // env: RAPIDLOGIN_GUARD_ROUTES="admin.*:web,client.*:client"
    'guard_routes' => str(env('RAPIDLOGIN_GUARD_ROUTES'))
        ->explode(',')
        ->filter()
        ->mapWithKeys(function ($item) {
            [$pattern, $guard] = str($item)->explode(':', 2);
            return [$pattern => $guard];
        })
        ->toArray(),

    // Per-guard model and users. Only needed for guards OTHER than the default guard
    // (the default guard reuses the top-level `users`/`user_model`/`user_route_key_name`).
    // Any omitted key falls back: `model` is derived from config/auth.php for the guard,
    // `users` is auto-fetched (first 3 from the model), `route_key_name` defaults to 'id',
    // and `home_route` falls back to `home_route_name`.
    'guards' => [
        // 'web' => [
        //     'model'          => App\Models\Admin::class,
        //     'route_key_name' => 'id',
        //     'users'          => [1 => 'super admin'],
        //     'home_route'     => 'admin.dashboard',
        // ],
    ],

];

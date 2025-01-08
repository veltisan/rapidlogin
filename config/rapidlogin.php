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
];

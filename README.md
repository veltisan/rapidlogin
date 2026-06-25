# rapidlogin
A Laravel package that enhances developer experience by adding one-click user switching buttons to the screen, streamlining authentication during local development.

## Installation
⚠️ This package is not to be used in production.
```
composer require --dev veltisan/rapidlogin
```

## Basic configuration
Add the following to your `.env` file
```
#bare minimum enable rapidlogin and to show the buttons
RAPIDLOGIN_ENABLED=true

#to define the users that will be displayed in the buttons (see Users section below)
#the format is `id:name,id:name,...`
#RAPIDLOGIN_USERS="1:John Doe,1337:Jane Doe"

#set to false to hide the close button from the panel
#RAPIDLOGIN_SHOW_CLOSE_BUTTON=true

#change the route the user is redirected to after logging via the button. defaults to 'home'
#RAPIDLOGIN_HOME_ROUTE_NAME=home

#to define the routes on which the buttons are displayed
#examples: 'login', 'login*', ... defaults to '*' to match all routes. Separate multiple route names with a comma.
#RAPIDLOGIN_ROUTE_NAME_PATTERN='*'

#to add additional links to the rapidlogin panel, use the format `text:url,text:url,...`
#RAPIDLOGIN_LINKS="Docs:https://docs.example.com,Horizon:https://horizon.example.com"
```

### Users
By default, rapid login buttons for first 3 users from dabatase will be displayed.
You can set the users in `.env` file, but if you want to set the users in a more clean format, publish the config (see below) and set the `users` value.

`key` is the user's `id` in database, and `value` is displayed in the button

```
'users' => [
    1 => 'admin',
    3 => 'manager',
    5 => 'customer',
],
```

### Additional links
You can add optional additional links to the rapidlogin panel by adding them to the `.env` file. This is a quick way to add handy shortcuts to frequently used development tools and resources directly in the panel.

### Multiple guards
By default rapidlogin authenticates against your app's default guard. If your app uses several guards, you can have the panel log into a different guard depending on which route it is shown on.

Map route-name patterns to a guard via `.env` (the first matching pattern wins; unmatched routes use the default guard):
```
#log into the 'web' guard on admin.* routes, the 'client' guard everywhere else (the default)
RAPIDLOGIN_GUARD_ROUTES="admin.*:web"

#optionally override which guard is used when no pattern matches (defaults to config('auth.defaults.guard'))
#RAPIDLOGIN_DEFAULT_GUARD=client
```

Different guards usually authenticate different models, so for any guard other than the default you define its model and users in the published config (`config/rapidlogin.php`). The default guard keeps using the top-level `users`/`user_model`/`user_route_key_name`.
```
'guards' => [
    'web' => [
        'model'          => App\Models\Admin::class,
        'route_key_name' => 'id',
        'users'          => [1 => 'super admin'],
        'home_route'     => 'admin.dashboard', // optional, defaults to home_route_name
    ],
],
```
Any per-guard key you omit falls back automatically: `model` is derived from `config/auth.php` for that guard, `users` is auto-fetched (first 3 from the model), `route_key_name` defaults to `id`, and `home_route` falls back to `home_route_name`.

## Publishing config and view
```
php artisan vendor:publish --tag=rapidlogin-config
```

This publishes a `blade view`, you are free to modify it to fit with your layout.
```
php artisan vendor:publish --tag=rapidlogin-views
```


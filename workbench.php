<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Workbench Environment Path
    |--------------------------------------------------------------------------
    |
    | This is the relative path from your package's root directory to the
    | workbench directory. By default, this is `workbench`, but you can
    | change this to whatever you want.
    |
    */

    'path' => 'workbench',

    /*
    |--------------------------------------------------------------------------
    | Workbench Databases
    |--------------------------------------------------------------------------
    |
    | Here you may specify a list of database connections that should be
    | available to the workbench. By default, this is just a single
    | SQLite database, but you can add as many as you want.
    |
    */

    'database' => [
        'default' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Workbench Migrations
    |--------------------------------------------------------------------------
    |
    | Here you may specify a list of migrations that should be run when
    | the workbench is created. By default, this is just the default
    | Laravel migrations, but you can add your package's migrations
    | as well.
    |
    */

    'migrations' => [
        'run' => true,
        'paths' => [
            //
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Workbench Seeders
    |--------------------------------------------------------------------------
    |
    | Here you may specify a list of seeders that should be run when
    | the workbench is created. By default, this is empty, but you
    | can add your package's seeders as well.
    |
    */

    'seeders' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Workbench Cache
    |--------------------------------------------------------------------------
    |
    | This option controls whether the workbench will cache the application
    | configuration and routes. This can speed up the workbench, but it
    | can also cause issues if you are making changes to the code.
    |
    */

    'cache' => [
        'config' => env('WORKBENCH_CACHE_CONFIG', false),
        'routes' => env('WORKBENCH_CACHE_ROUTES', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Workbench User
    |--------------------------------------------------------------------------
    |
    | This is the model that will be used for the authenticated user when
    | running tests. You can change this to your own user model if
    | you have one.
    |
    */

    'user' => 'Illuminate\Foundation\Auth\User',

    /*
    |--------------------------------------------------------------------------
    | Workbench Listeners
    |--------------------------------------------------------------------------
    |
    | This is a list of event listeners that will be registered with the
    | workbench. You can use this to listen for events that are
    | fired by your package or by Laravel itself.
    |
    */

    'listeners' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Workbench Providers
    |--------------------------------------------------------------------------
    |
    | This is a list of service providers that will be registered with the
    | workbench. You can use this to register your package's service
    | provider, as well as any other providers that your package
    | may need to function correctly.
    |
    */

    'providers' => [
        \Orchestra\Workbench\WorkbenchServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Workbench Aliases
    |--------------------------------------------------------------------------
    |
    | This is a list of class aliases that will be registered with the
    which will be automatically registered on the workbench.
    |
    */

    'aliases' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Workbench Environment Variables
    |--------------------------------------------------------------------------
    |
    | This is a list of environment variables that will be loaded into
    | the workbench. You can use this to override the default
    | Laravel environment variables.
    |
    */

    'env' => [
        // 'FOO' => 'bar',
    ],

    /*
    |--------------------------------------------------------------------------
    | Workbench Install
    |--------------------------------------------------------------------------
    |
    | This option controls whether the workbench will be installed when
    | you run `composer install`. This is useful if you want to
    | quickly set up a fresh workbench.
    |
    */

    'install' => env('WORKBENCH_INSTALL', false),

    /*
    |--------------------------------------------------------------------------
    | Workbench Start
    |--------------------------------------------------------------------------
    |
    | This is the default start path for the workbench server. You can
    | change this to whatever you want.
    |
    */

    'start_path' => '/',
];

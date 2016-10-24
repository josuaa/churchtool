<?php

namespace App\Providers;

use DB;
use Dingo\Api\Transformer\Adapter\Fractal;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*DB::listen(function ($query) {
            var_dump($query->sql);
        });*/

        // Use the JsonApiSerializer instead of the default DataSerializer to serialize
        // JSON responses.
        $this->app['Dingo\Api\Transformer\Factory']->setAdapter(
            function ($app) {
                $fractal = new Manager();
                $fractal->setSerializer(new JsonApiSerializer());

                return new Fractal($fractal, 'include', ',');
            }
        );

        // Register a handler for the ModelNotFoundException, that occurs for example if an
        // invalid model id is passed via a route.
        $this->app['Dingo\Api\Exception\Handler']->register(
            function (ModelNotFoundException $e) {
                return \Response::make(
                    [
                        'message' => '404 Resource ('.$e->getModel().') not found',
                        'status_code' => 404,
                    ],
                    404
                );
            }
        );

        // Enable foreign keys for SQLITE
        // taken from http://stackoverflow.com/questions/31228950/laravel-5-1-enable-sqlite-foreign-key-constraints
        if (DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
            DB::statement(DB::raw('PRAGMA foreign_keys=1'));
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}

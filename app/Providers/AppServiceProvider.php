<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        // this way for forget fillables and guardeds
        Model::unguard();

        Response::macro('success', function ($msg, $data = null) {
            return response()->json([
                'success' => true,
                'msg' => $msg,
                'data' => $data
            ], 200);
        });

        Response::macro('error', function ($msg, $status_code = 400) {
            return response()->json([
                'success' => false,
                'msg' => $msg,
            ], $status_code);
        });

        Response::macro('withCookie', function ($msg, $cookie) {
            return response([
                'success' => true,
                'msg' => $msg,
            ])->withCookie($cookie);
        });
    }
}

<?php
namespace App\Libraries\ApiResponse;

use Illuminate\Support\ServiceProvider;

class ApiResponseServiceProvider extends ServiceProvider
{

    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind('ApiResponse', function() {
            return new \App\Libraries\ApiResponse\ApiResponse;
        });
    }
}

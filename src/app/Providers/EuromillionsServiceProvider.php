<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Euromillions\Services\HttpProxy;
use Euromillions\Services\Cache;
use Euromillions\Contracts\IResultsApi;
use Tests\Unit\Euromillions\Services\HtppProxyMock;

class EuromillionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(HttpProxy $httpProxy)
    {
        $this->app->bind(
            'Euromillions\Contracts\IResultsApi',
            'Euromillions\Services\HttpProxy'
        );

        $this->app->singleton(Cache::class, function ($app) use ($httpProxy) {
            return new Cache($httpProxy);
        });

        $this->app->bind(
            'Euromillions\Contracts\ICache',
            'Euromillions\Services\Cache'
        );

        $this->app->bind(
            'Euromillions\Contracts\ICacheWithMockProxy',
            function ($app) {
                $httpProxy = new HtppProxyMock();
                 return new Cache($httpProxy);
            }
        );

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(HttpProxy::class, function ($app) {
            return new HttpProxy(config('euromillions.apiUrls'));
        });
    }
}

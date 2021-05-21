<?php

namespace Uasoft\Badaso\Module\Sitemap\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Uasoft\Badaso\Middleware\ApiRequest;
use Uasoft\Badaso\Module\Content\BadasoSitemapModule;
use Uasoft\Badaso\Module\Sitemap\Facades\BadasoSitemapModule as FacadesBadasoSitemapModule;
use Uasoft\Badaso\Module\Sitemap\Middleware\CaptureRequestMiddleware;

class BadasoModuleSitemapServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('BadasoSitemapModule', FacadesBadasoSitemapModule::class);

        $router = $this->app->make(Router::class);
        $router->pushMiddlewareToGroup(ApiRequest::class, CaptureRequestMiddleware::class);

        $this->app->singleton('badaso-sitemap-module', function () {
            return new BadasoSitemapModule();
        });

        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConsoleCommands();
    }

    /**
     * Register the commands accessible from the Console.
     */
    private function registerConsoleCommands()
    {
        // $this->commands(BadasoContentSetup::class);
    }
}

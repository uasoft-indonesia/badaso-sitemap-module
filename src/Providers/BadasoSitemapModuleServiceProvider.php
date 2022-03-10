<?php

namespace Uasoft\Badaso\Module\Sitemap\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Tests\Feature\BadasoSiteMapTest;
use Uasoft\Badaso\Module\Sitemap\BadasoSitemapModule;
use Uasoft\Badaso\Module\Sitemap\Commands\BadasoSitemapSetup;
use Uasoft\Badaso\Module\Sitemap\Commands\BadasoSitemapTestSetup;
use Uasoft\Badaso\Module\Sitemap\Facades\BadasoSitemapModule as FacadesBadasoSitemapModule;

class BadasoSitemapModuleServiceProvider extends ServiceProvider
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

        $this->app->singleton('badaso-sitemap-module', function () {
            return new BadasoSitemapModule();
        });

        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        $this->publishes([
            __DIR__.'/../Config/badaso-sitemap.php' => config_path('badaso-sitemap.php'),
        ], 'badaso-sitemap-config');
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
        $this->commands(BadasoSitemapSetup::class);
        $this->commands(BadasoSitemapTestSetup::class);
    }
}

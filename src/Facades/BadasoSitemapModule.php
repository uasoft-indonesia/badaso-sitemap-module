<?php

namespace Uasoft\Badaso\Module\Sitemap\Facades;

use Illuminate\Support\Facades\Facade;

class BadasoSitemapModule extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'badaso-content-module';
    }
}

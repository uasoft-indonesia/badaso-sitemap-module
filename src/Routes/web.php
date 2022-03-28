<?php

use Illuminate\Support\Facades\Route;

$api_route_prefix = config('badaso-sitemap.prefix');

Route::group(
    [
        'prefix'     => $api_route_prefix,
        'namespace'  => 'Uasoft\Badaso\Module\Sitemap\Controllers',
        'as'         => 'badaso.module.sitemap.',
        'middleware' => 'web',
    ],
    function () {
        Route::get('/sitemap.xml', 'SitemapController@get')->name('get');
        Route::get('/{prefix}/sitemap.xml', 'SitemapController@prefixGet')->name('prefix.get');
        Route::get('/{prefix}/{page}/sitemap.xml', 'SitemapController@prefixPageGet')->name('prefix.page.get');
    }
);

<?php

namespace Uasoft\Badaso\Module\Sitemap\Controllers;

use App\Http\Controllers\Controller;
use Uasoft\Badaso\Module\Sitemap\Helper\SitemapXMLFormat;
use Uasoft\Badaso\Module\Sitemap\Helper\WebAccessHandle;

class SitemapController extends Controller
{
    public function get()
    {
        $sitemap = config('badaso-sitemap.sitemap');
        $is_use_blog_module = in_array('badaso-content-module', explode(',', env('MIX_BADASO_PLUGINS')));

        $array_response = [];
        foreach ($sitemap as $prefix => $value) {
            if (!$is_use_blog_module) {
                if ($prefix == 'badaso-blog') {
                    continue;
                }
            }
            $route_access = route('badaso.module.sitemap.prefix.get', ['prefix' => $prefix]);
            $array_response[$route_access] = [
                'loc' => $route_access,
            ];
        }

        return $this->xmlSuccessResponse(SitemapXMLFormat::defaultFormatSitemapIndexXML($array_response));
    }

    public function prefixGet($prefix)
    {
        $web_access = new WebAccessHandle($prefix, null);

        return $this->xmlSuccessResponse($web_access->generateViewXML());
    }

    public function prefixPageGet($prefix, $page)
    {
        $web_access = new WebAccessHandle($prefix, $page);

        return $this->xmlSuccessResponse($web_access->generateViewXML());
    }

    private function xmlSuccessResponse($xml)
    {
        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}

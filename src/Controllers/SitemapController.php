<?php

namespace Uasoft\Badaso\Module\Sitemap\Controllers;

use Illuminate\Routing\Controller;
use Uasoft\Badaso\Module\Sitemap\Helper\SitemapXMLFormat;
use Uasoft\Badaso\Module\Sitemap\Helper\WebAccessHandle;

class SitemapController extends Controller
{
    public function get()
    {
        $sitemap = config('badaso-sitemap.sitemap');
        $is_use_blog_module = in_array('badaso-content-module', explode(',', env('MIX_BADASO_MODULES')));

        $array_response = [];
        foreach ($sitemap as $prefix => $value) {
            if (! $is_use_blog_module) {
                if ($prefix == 'badaso-blog') {
                    continue;
                }
            }
            $route_access = route('badaso.module.sitemap.prefix.get', ['prefix' => $prefix]);
            $array_response[$route_access] = [
                'loc' => $route_access,
            ];
        }

        $custom_sitemap = config('badaso-sitemap.custom_sitemap');
        foreach ($custom_sitemap as $prefix => $value) {
            $route_access = route('badaso.module.sitemap.prefix.get', ['prefix' => $prefix]);
            $array_response[$route_access] = [
                'loc' => $route_access,
            ];
        }

        return $this->xmlSuccessResponse(SitemapXMLFormat::defaultFormatSitemapIndexXML($array_response));
    }

    public function prefixGet($prefix)
    {
        $sitemap = config('badaso-sitemap.sitemap');
        $sitemap_keys = array_keys($sitemap);

        $custom_sitemap = config('badaso-sitemap.custom_sitemap');
        $custom_sitemap_keys = array_keys($custom_sitemap);

        if (in_array($prefix, $sitemap_keys)) {
            $web_access = new WebAccessHandle($prefix, null);

            return $this->xmlSuccessResponse($web_access->generateViewXML());
        } elseif (in_array($prefix, $custom_sitemap_keys)) {
            $custom_sitemap_value = $custom_sitemap[$prefix];

            foreach ($custom_sitemap_value as $loc => $value) {
                $custom_sitemap_value[$loc]['loc'] = url($loc);

                $custom_sitemap_value[$loc] = array_reverse($custom_sitemap_value[$loc]);
            }

            return $this->xmlSuccessResponse(SitemapXMLFormat::defaultFormatSitemapURLSetXML($custom_sitemap_value));
        } else {
            abort(404);
        }
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

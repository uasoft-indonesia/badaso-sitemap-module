<?php

namespace Uasoft\Badaso\Module\Sitemap\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BadasoSiteMapTest extends TestCase
{
    public function test_count_sitemap()
    {
        if (strpos(env('MIX_BADASO_MODULES'), 'post-module') == 0) {
            $response = file_get_contents(env('APP_URL').'/sitemap.xml');
            $xml = simplexml_load_string($response);

            $sitemap = [];
            foreach ($xml as $key => $value) {
                $loc = ((array) $value->loc)[0];
                $sitemap[] = $loc;
            }
            $this->assertTrue($xml->count() > 0);
        }
    }

    public function test_check_same_group_sitemap()
    {
        if (strpos(env('MIX_BADASO_MODULES'), 'post-module') == 0) {
            $data = config('badaso-sitemap.sitemap');
            $keySitemap = [];

            foreach ($data as $key => $value) {
                $keySitemap[] = env('APP_URL')."/$key".'/sitemap.xml';
            }

            $response = file_get_contents(env('APP_URL').'/sitemap.xml');
            $xml = simplexml_load_string($response);

            $sitemap = [];
            foreach ($xml as $key => $value) {
                $loc = ((array) $value->loc)[0];
                $sitemap[] = $loc;
            }

            $this->assertNotEmpty($keySitemap == $sitemap);
        }
    }

    public function test_check_same_data_sitemap()
    {
        if (strpos(env('MIX_BADASO_MODULES'), 'post-module') == 0) {
            $temp = config('badaso-sitemap.sitemap');
            $keySitemap = [];

            foreach ($temp as $key => $value) {
                $keySitemap[] = [$key, $value];
            }

            foreach ($keySitemap as $key => $value) {
                $data = config('badaso-sitemap.sitemap')[$value[0]]['web-access']['url'];
                $nonslug = substr($data, strpos($data, '/:'), );
                $slug = str_replace('/:', '', $nonslug);

                $path = str_replace("$nonslug", '/', $data);

                $response = file_get_contents(env('APP_URL')."/$value[0]/sitemap.xml");
                $xml = simplexml_load_string($response);
                $sitemaparr = [];
                $lastmodarr = [];

                $postDB = DB::table($value[1]['table'])->get();

                foreach ($xml as $key => $value) {
                    $loc = ((array) $value->loc)[0];
                    $lastmod = ((array) $value->lastmod)[0];
                    $sitemaparr[] = $loc;
                    $lastmodarr[] = $lastmod;
                }

                $sitemap = collect($sitemaparr);
                $lastmod = collect($lastmodarr);

                foreach ($postDB as $key => $value) {
                    $loc = env('APP_URL')."$path".$value->$slug;

                    $last = substr(str_replace('T', ' ', $value->created_at), 0, 19);

                    $sitemap_data = $sitemap->first(function ($item) use ($loc) {
                        return $item == $loc;
                    });

                    $lastmod_data = $lastmod->first(function ($items) use ($last) {
                        $item = substr(str_replace('T', ' ', $items), 0, 19);

                        return $item == $last;
                    });

                    $this->assertNotEmpty($sitemap_data);
                }
                $this->assertNotEmpty($lastmod_data);
            }
        }
    }
}

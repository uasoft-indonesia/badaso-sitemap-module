<?php

namespace Uasoft\Badaso\Module\Sitemap\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;
use Uasoft\Badaso\Helpers\CallHelperTest;
use Uasoft\Badaso\Module\Post\Models\Category;
use Uasoft\Badaso\Module\Post\Models\Post;
use Uasoft\Badaso\Module\Post\Models\Tag;

class BadasoSiteMapTest extends TestCase
{
    public function test_count_sitemap()
    {
        if (strpos(env('MIX_BADASO_MODULES'), 'post-module') == 0) {

            $response = $this->get("/sitemap.xml");

            $response = ($response->getContent());

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

             $response = $this->get("/sitemap.xml");

            $response = ($response->getContent());
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
        $token = CallHelperTest::login($this);
        $tableCategory = Category::latest()->first();

        $request_data = [
            'title'=> 'Example Category',
            'parentId'=> null,
            'metaTitle'=> 'example',
            'slug'=> Str::random(10),
            'content'=> 'An example of create new category.',
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")->post(CallHelperTest::getApiV1('/category/add'), $request_data);

        $request_data = [
            'title' => Str::random(10),
            'metaTitle' => Str::random(10),
            'slug' => Str::random(10),
            'content' => Str::random(10),
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")->json('POST', CallHelperTest::getApiV1('/tag/add'), $request_data);
        $response->assertSuccessful();

        $tableTag = Tag::latest()->first();

        $tableCategory = Category::latest()->first();
        $count = 5;
        for ($i = 0; $i < $count; $i++) {
            $request_data = [
                'title' => Str::random(40),
                'slug' => $tableCategory->slug == $tableCategory->slug ? Str::random(40) : $tableCategory->slug,
                'content' => Str::random(40),
                'metaTitle' => Str::random(40),
                'metaDescription' => Str::random(40),
                'summary' => Str::random(40),
                'published' => true,
                'tags' => [
                    $tableTag->id,
                ],
                'category' => $tableCategory->id,
                'thumbnail' => 'https://badaso-web.s3-ap-southeast-1.amazonaws.com/files/shares/1619582634819_badaso.png',
            ];
            $response = $this->withHeader('Aut', "Bearer $token")->post(CallHelperTest::getApiV1('/post/add'), $request_data);
            $response->assertSuccessful();
        }

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

                $response = $this->get("/$value[0]/sitemap.xml");

                $response = ($response->getContent());
                $xml = simplexml_load_string($response);
                $sitemaparr = [];
                $lastmodarr = [];
                $postDB = DB::table($value[1]['table'])->get();
                
                foreach ($xml as $key => $value) {
                    if(count((array) $xml) > 1){
                    $loc = ((array) $value->loc)[0];
                    $lastmod = ((array) $value->lastmod)[0];
                    $sitemaparr[] = $loc;
                    $lastmodarr[] = $lastmod;
                    }else{
                    $loc = ((array) $value->loc)[0];
                    $lastmod = ((array) $value->lastmod);
                    $sitemaparr[] = $loc;
                    $lastmodarr[] = $lastmod;
                    }
                    
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
        $tablePost = Post::orderBy('id', 'desc')
                    ->limit(5)
                    ->get();

        $ids = [];
        foreach ($tablePost as $key => $value) {
            $ids[] = $value->id;
        }

        $tableCategory = Category::latest()->first();

        $id = [
            'id' => "$tableCategory->id",
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")->delete(CallHelperTest::getApiV1('/category/delete'), $id);
        $response->assertSuccessful();

        $response = $this->withHeader('Authorization', "Bearer $token")->delete(CallHelperTest::getApiV1('/post/delete-multiple'), [
            'ids' => join(',', $ids),
        ]);
        $response->assertStatus(200);
        $tableTag = Tag::latest()->first();
        $request_data = [
            'id' => "$tableTag->id",
        ];
        $response = $this->withHeader('Authorization', "Bearer $token")->json('DELETE', CallHelperTest::getApiV1('/tag/delete'), $request_data);
    }
}

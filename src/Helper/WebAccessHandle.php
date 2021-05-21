<?php

namespace Uasoft\Badaso\Module\Sitemap\Helper;

use Illuminate\Support\Facades\DB;

class WebAccessHandle
{
    public static $max_content = 500;
    private $web_access;
    private $page;
    private $prefix;
    private $attribute_sub_url;

    public function __construct($prefix_sitemap, $page)
    {
        $this->web_access = config('badaso-sitemap.sitemap')[$prefix_sitemap]['web-access']['url'];
        $this->page = $page;
        $this->prefix = $prefix_sitemap;
        $this->explodePathUrl();
        $this->generateViewXML();
    }

    public function explodePathUrl()
    {
        $explode_path_url = explode('/', $this->web_access);
        foreach ($explode_path_url as $index => $sub_url) {
            if (substr($sub_url, 0, 1) == ':') {
                $sub_url = substr($sub_url, 1);
                $table_name = config('badaso-sitemap.sitemap')[$this->prefix]['table'];
                $field_name = $sub_url;

                $available_list = DB::table($table_name)->select($field_name, 'created_at', 'updated_at');
                $count_available_list = $available_list->count();
                $max_pages = self::$max_content;
                $num_pages = intval(ceil($count_available_list / $max_pages));

                $pages[0] = [
                    'offset' => 0,
                    'limit' => $max_pages,
                ];

                for ($i = 1; $i < $num_pages; ++$i) {
                    $offset = $pages[intval($i - 1)]['offset'] + ($max_pages);
                    $limit = $i != $num_pages - 1 ? $max_pages : $count_available_list - $offset;
                    $pages[$i] = [
                        'offset' => $offset,
                        'limit' => $limit,
                    ];
                }

                $explode_path_url[$index] = [
                    'sub_url' => $sub_url,
                    'available_list' => $available_list,
                    'count_available_list' => $count_available_list,
                    'num_pages' => $num_pages,
                    'pages' => $pages,
                    'field_name' => $field_name,
                ];
            }
        }

        $this->attribute_sub_url = $explode_path_url;
    }

    public function generateViewXML()
    {
        if (isset($this->page)) {
            return $this->generatePageUrl();
        } else {
            return $this->generatePageGroup();
        }
    }

    public function generatePageUrl()
    {
        $generate_page_url = [];
        foreach ($this->attribute_sub_url as $index_path => $explode_path_url) {
            if (is_array($explode_path_url)) {
                if (array_key_exists($this->page - 1, $explode_path_url['pages'])) {
                    $page = $explode_path_url['pages'][$this->page - 1];
                } else {
                    $page = $explode_path_url['pages'][0];
                }

                $limit = $page['limit'];
                $offset = $page['offset'];
                $field_name = $explode_path_url['field_name'];
                $model_table = $explode_path_url['available_list']->offset($offset)->limit($limit)->get();

                foreach ($model_table as $idx_model => $row) {
                    $row = (array) $row;
                    foreach ($this->attribute_sub_url as $sub_index_path => $sub_explode_path_url) {
                        if (is_array($sub_explode_path_url)) {
                            $generate_page_url[$idx_model]['path'][$index_path] = $row[$field_name];
                            $generate_page_url[$idx_model]['lastmod'] = \Carbon\Carbon::parse($row['updated_at'])->toISOString();
                        } else {
                            $generate_page_url[$idx_model]['path'][$sub_index_path] = $sub_explode_path_url;
                        }
                    }
                }
            }
        }

        $new_generate_page_array_xml_format = [];
        foreach ($generate_page_url as $key => $page_url) {
            $path = join('/', $page_url['path']);
            $loc_url = url($path);
            $new_generate_page_array_xml_format[$loc_url] = [
                'loc' => $loc_url,
                'lastmod' => $page_url['lastmod'],
            ];
        }

        return SitemapXMLFormat::defaultFormatSitemapIndexXML($new_generate_page_array_xml_format);
    }

    public function generatePageGroup()
    {
        $generate_page_group = [];
        foreach ($this->attribute_sub_url[1]['pages'] as $index => $explode) {
            $page = $index + 1;
            $loc = route('badaso.module.sitemap.prefix.page.get', ['prefix' => $this->prefix, 'page' => $page]);
            $generate_page_group[$loc] = [
                'loc' => $loc,
            ];
        }

        return SitemapXMLFormat::defaultFormatSitemapIndexXML($generate_page_group);
    }
}

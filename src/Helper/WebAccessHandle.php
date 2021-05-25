<?php

namespace Uasoft\Badaso\Module\Sitemap\Helper;

use Illuminate\Support\Facades\DB;

class WebAccessHandle
{
    private $web_access;
    private $page;
    private $prefix;
    private $attribute_sub_url;

    public function __construct($prefix_sitemap, $page)
    {
        try {
            $this->web_access = config('badaso-sitemap.sitemap')[$prefix_sitemap]['web-access']['url'];
        } catch (\Exception $e) {
            abort(404);
        }
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

                $available_list = DB::table($table_name);

                $explode_sub_url = explode(',', $sub_url);
                if (count($explode_sub_url) == 3) {
                    $field_name_foreign = trim($explode_sub_url[0]);
                    $field_name_references = trim($explode_sub_url[1]);
                    [$table_references] = explode('.', $field_name_references);
                    $field_name = trim($explode_sub_url[2]);

                    $available_list = $available_list->join($table_references, $field_name_foreign, $field_name_references)->select($field_name, "{$table_name}.created_at", "{$table_name}.updated_at");
                } else {
                    $available_list = $available_list->select($field_name, 'created_at', 'updated_at');
                }

                $count_available_list = $available_list->count();
                $max_pages = intval(config('badaso-sitemap.max_content_paginate'));
                if ($max_pages == null || $max_pages == 0) {
                    $max_pages = $count_available_list;
                }
                $num_pages = intval(ceil($count_available_list / $max_pages));

                $pages[0] = [
                    'offset' => 0,
                    'limit'  => $max_pages,
                ];

                for ($i = 1; $i < $num_pages; $i++) {
                    $offset = $pages[intval($i - 1)]['offset'] + ($max_pages);
                    $limit = $i != $num_pages - 1 ? $max_pages : $count_available_list - $offset;
                    $pages[$i] = [
                        'offset' => $offset,
                        'limit'  => $limit,
                    ];
                }

                $explode_path_url[$index] = [
                    'sub_url'              => $sub_url,
                    'available_list'       => $available_list,
                    'count_available_list' => $count_available_list,
                    'num_pages'            => $num_pages,
                    'pages'                => $pages,
                    'field_name'           => $field_name,
                ];
            }
        }

        $this->attribute_sub_url = $explode_path_url;
    }

    public function generateViewXML()
    {
        foreach ($this->attribute_sub_url as $index => $path_url) {
            if (is_array($path_url)) {
                if ($path_url['num_pages'] == 1) {
                    return $this->generatePageUrl();
                }

                if (isset($this->page)) {
                    return $this->generatePageUrl();
                } else {
                    return $this->generatePageGroup();
                }
                break;
            }
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
                            if (!array_key_exists($field_name, $row)) {
                                $field_name = explode('.', $field_name)[1];
                            }
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
                'loc'     => $loc_url,
                'lastmod' => $page_url['lastmod'],
            ];
        }

        return SitemapXMLFormat::defaultFormatSitemapIndexXML($new_generate_page_array_xml_format);
    }

    public function generatePageGroup()
    {
        $generate_page_group = [];
        foreach ($this->attribute_sub_url as $index => $path_url) {
            if (is_array($path_url)) {
                foreach ($path_url['pages'] as $index => $explode) {
                    $page = $index + 1;
                    $loc = route('badaso.module.sitemap.prefix.page.get', ['prefix' => $this->prefix, 'page' => $page]);
                    $generate_page_group[$loc] = [
                        'loc' => $loc,
                    ];
                }

                break;
            }
        }

        return SitemapXMLFormat::defaultFormatSitemapIndexXML($generate_page_group);
    }
}

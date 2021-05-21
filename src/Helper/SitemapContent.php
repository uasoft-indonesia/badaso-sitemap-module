<?php

namespace Uasoft\Badaso\Module\Sitemap\Helper;

use Illuminate\Support\Facades\File;

class SitemapContent
{
    private $path_file;
    public $xml_sitemap_content;
    public $sitemaps;

    public function __construct()
    {
    }

    public function defaultFormatSitemapXML(): self
    {
        $this->xml_sitemap_content = SitemapXMLFormat::defaultFormatSitemapXML($this->sitemaps);

        return $this;
    }

    public function get()
    {
        return $this->defaultFormatSitemapXML()->xml_sitemap_content;
    }

    public function add($loc): self
    {
        $lastmod = date('c');
        $this->sitemaps[$loc] = [
            'loc' => $loc,
            'lastmod' => $lastmod,
        ];

        return $this;
    }

    public function remove($loc)
    {
        unset($this->sitemaps[$loc]);

        return $this;
    }

    public function save(): self
    {
        $str_json_sitemaps = json_encode($this->sitemaps);

        File::put($this->path_file, $str_json_sitemaps);

        return $this;
    }

    public function loadContentSitemapJSONFormat($storage_sitemap_path)
    {
        $directory_name = 'sitemap';
        File::isDirectory(storage_path($directory_name)) or File::makeDirectory(storage_path($directory_name), 0777, true, true);
        $this->path_file = storage_path("{$directory_name}/{$storage_sitemap_path}");

        if (File::exists($this->path_file)) {
            $sitemaps = json_decode(file_get_contents($this->path_file), true);
        } else {
            File::put($this->path_file, '{}');
            $sitemaps = [];
        }
        $this->sitemaps = $sitemaps;

        return $this;
    }

    public static function load($storage_sitemap_path)
    {
        $sitemap_XML = new self();
        $sitemap_XML = $sitemap_XML->loadContentSitemapJSONFormat($storage_sitemap_path);

        return $sitemap_XML;
    }
}

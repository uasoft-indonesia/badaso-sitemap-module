<?php

namespace Uasoft\Badaso\Module\Sitemap\Helper;

class SitemapXMLFormat
{
    public static function defaultFormatSitemapURLSetXML($array_data)
    {
        $default_content_xml = '';

        foreach ($array_data as $key => $value) {
            $xml_content = '';
            foreach ($value as $keyTag => $valTag) {
                $xml_content .= "<$keyTag>$valTag</{$keyTag}>";
            }
            $xml_content = "<url>{$xml_content}</url>";
            $default_content_xml .= $xml_content;
        }

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">{$default_content_xml}</urlset>";
    }

    public static function defaultFormatSitemapIndexXML($array_data)
    {
        $default_content_xml = '';

        foreach ($array_data as $key => $value) {
            $xml_content = '';
            foreach ($value as $keyTag => $valTag) {
                $xml_content .= "<$keyTag>$valTag</{$keyTag}>";
            }
            $xml_content = "<sitemap>{$xml_content}</sitemap>";
            $default_content_xml .= $xml_content;
        }

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">{$default_content_xml}</sitemapindex>";
    }
}

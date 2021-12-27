---
sidebar_position: 2
---

# Configuration

File `config.php` configuration

```
return [
    'max_content_paginate' => 500,
    'prefix' => '/',
    'sitemap' => [
        'badaso-blog' => [
            'table' => 'posts',
            'web-access' => [
                'url' => env('MIX_BLOG_POST_URL_PREFIX').'/:slug',
                // :slug is a field in table posts
                // this url auto generate according to the number of table rows
            ],
        ],
        ...
    ],
    'custom_sitemap' => [
        'root' => [
            '/' => [
              'lastmod' => '2021-05-24T09:32:52.785Z',
            ],
            'sub-path' => [
              'lastmod' => '2021-05-24T09:32:52.785Z',
            ],
            'sub-path/sub-path-other' => [
              'lastmod' => '2021-05-24T09:32:52.785Z',
            ],
            ...
        ],
        ...
    ],
];
```
Your can get foreign table attribute 
```
...
'sitemap' => [
    'badaso-blog' => [
        'table' => 'posts',
        'web-access' => [
            'url' => env('MIX_BLOG_POST_URL_PREFIX').'/:posts.category_id,category.id,category.title',
            // posts.category_id  => foreign key posts table field category_id
            // category.id        => posts table category_id field reference to table category field id 
            // category.title     => output value to path url from category table title field
            // output : http://{HOST}/{MIX_BLOG_POST_URL_PREFIX}/business
        ],
    ],
    ...
],
...
```
You can create custom sitemap url
```
...
'custom_sitemap' => [
     // group by http://{HOST}/roo/sitemap.xml
    'root' => [
         // http://{HOST}/root
        '/' => [
          'lastmod' => '2021-05-24T09:32:52.785Z',
        ],
         // http://{HOST}/sub-path
        'sub-path' => [
          'lastmod' => '2021-05-24T09:32:52.785Z',
        ],
         // http://{HOST}/sub-path/sub-path-othe
        'sub-path/sub-path-other' => [
          'lastmod' => '2021-05-24T09:32:52.785Z',
        ],
        ...
    ],
    ...
],
...
```

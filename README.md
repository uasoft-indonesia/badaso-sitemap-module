# badaso-sitemap-module
## Installation
1. <a href="https://badaso-docs.uatech.co.id/docs/en/getting-started/installation/" target="blank"> Install Badaso </a> from laravel project
2. Install badaso content module `composer require uasoft-indonesia/badaso-sitemap-module` 
3. Set env

```
MIX_DEFAULT_MENU=admin
MIX_BADASO_MENU=${MIX_DEFAULT_MENU},badaso-sitemap-module
MIX_BADASO_PLUGINS=badaso-sitemap-module
```
4. Call command `php artisan badaso-sitemap:setup`
5. Call command `php artisan serve`
6. In you browser, call url `http://localhost:8000/sitemap.xml`

## Configuration
file `config.php` configuration

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


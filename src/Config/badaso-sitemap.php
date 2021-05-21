<?php

return [
    'max_content_paginate' => 500,
    'prefix' => '/',
    'sitemap' => [
        'badaso-blog' => [
            'table' => 'posts',
            'web-access' => [
                'url' => env('MIX_BLOG_POST_URL_PREFIX').'/:slug',
            ],
            // 'web-access' => [
            //     'url' => env('MIX_BLOG_POST_URL_PREFIX').'/:slug/:posts.category_id,categories.id,categories.title/:posts.category_id,categories.id,categories.slug',
            // ],
        ],
        // 'user' => [
        //     'table' => 'users',
        //     'web-access' => [
        //         'url' => env('MIX_BLOG_POST_URL_PREFIX').'/:email',
        //     ],
        // ],
    ],
];

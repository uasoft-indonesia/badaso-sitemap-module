---
sidebar_position: 1
---

# Installation

1. Install [badaso core](https://badaso-docs.uatech.co.id/getting-started/installation) first, then install badaso sitemap module
    ```
    composer require badaso/sitemap-module
    ``` 
1. Add the plugins to your .env. If you have another plugins installed, include them using delimiter comma (,).
    ```
    MIX_BADASO_MENU=${MIX_DEFAULT_MENU},sitemap-module
    MIX_BADASO_PLUGINS=sitemap-module
    ```
1. Setup the sitemap module 
    ```
    php artisan badaso-sitemap:setup
    ```
1. Your sitemap is ready.
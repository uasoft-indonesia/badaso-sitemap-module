---
sidebar_position: 1
---

# Installation

1. Install [Badaso](https://badaso-docs.uatech.co.id/docs/en/getting-started/installation/) from laravel project
2. Install badaso sitemap module
```
composer require badaso/sitemap-module
``` 
3. Set env

```
MIX_DEFAULT_MENU=admin
MIX_BADASO_MENU=${MIX_DEFAULT_MENU},sitemap-module
MIX_BADASO_MODULES=sitemap-module
```
4. Call command 
```
php artisan badaso-sitemap:setup
```
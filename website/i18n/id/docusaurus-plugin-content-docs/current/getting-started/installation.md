---
sidebar_position: 1
---

# Installation

1. Install [badaso](https://badaso-docs.uatech.co.id/getting-started/installation) pada project laravel
2. Install badaso sitemap module
```
composer require badaso/sitemap-module
``` 
3. Atur .env file

```
MIX_DEFAULT_MENU=admin
MIX_BADASO_MENU=${MIX_DEFAULT_MENU},sitemap-module
MIX_BADASO_PLUGINS=sitemap-module
```
4. Penggil perintah pada terminal 
```
php artisan badaso-sitemap:setup
```
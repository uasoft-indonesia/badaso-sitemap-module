<?php

namespace Uasoft\Badaso\Module\Sitemap\Middleware;

use Closure;

class CaptureRequestMiddleware
{
    public function handle($request, Closure $next)
    {
        // Perform action
        return $next($request);
    }
}

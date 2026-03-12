<?php

namespace HolartWeb\HolartCMS\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use HolartWeb\HolartCMS\Services\PageDataService;

class SharePageData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip admin routes
        if ($request->is('admin/*')) {
            return $next($request);
        }

        try {
            // Resolve service from container (lazy load)
            $pageDataService = app(PageDataService::class);

            // Check if the current route has an inactive entity
            if ($pageDataService->hasInactiveEntity()) {
                abort(404, 'Page not found or is inactive');
            }

            // Get page data for current route
            $pageData = $pageDataService->getPageData();

            // Share with all views
            View::share('pageData', $pageData);
        } catch (\Exception $e) {
            // If service fails (DB not available), continue without page data
        }

        return $next($request);
    }
}

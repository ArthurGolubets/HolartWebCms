<?php

namespace HolartWeb\HolartCMS\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use HolartWeb\HolartCMS\Services\PageDataService;

class SharePageData
{
    protected PageDataService $pageDataService;

    public function __construct(PageDataService $pageDataService)
    {
        $this->pageDataService = $pageDataService;
    }

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

        // Check if the current route has an inactive entity
        if ($this->pageDataService->hasInactiveEntity()) {
            abort(404, 'Page not found or is inactive');
        }

        // Get page data for current route
        $pageData = $this->pageDataService->getPageData();

        // Share with all views
        View::share('pageData', $pageData);

        return $next($request);
    }
}

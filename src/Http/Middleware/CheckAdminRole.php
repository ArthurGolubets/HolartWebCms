<?php

namespace HolartWeb\HolartCMS\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use HolartWeb\HolartCMS\Enums\AdminRole;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $admin = $request->user('admin');

        if (!$admin) {
            abort(403, 'Unauthorized');
        }

        // Convert string roles to AdminRole enums
        $allowedRoles = array_map(fn($role) => AdminRole::from($role), $roles);

        // Check if admin has one of the allowed roles
        if (!in_array($admin->role, $allowedRoles)) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}

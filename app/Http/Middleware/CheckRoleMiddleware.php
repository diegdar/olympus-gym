<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        /** @var User|null $autheticatedUser */
        $autheticatedUser = Auth::user();
        if (!$autheticatedUser || !$autheticatedUser->hasRole($role)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}

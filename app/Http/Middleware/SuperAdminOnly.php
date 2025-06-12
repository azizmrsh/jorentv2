<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // التحقق من أن المستخدم مسجل دخول
        if (!auth()->check()) {
            abort(403, 'غير مصرح لك بالدخول');
        }

        // التحقق من أن المستخدم لديه دور super_admin
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'هذه الصفحة متاحة للمدير العام فقط');
        }

        return $next($request);
    }
}

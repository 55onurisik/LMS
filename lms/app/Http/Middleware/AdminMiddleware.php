<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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
        // Admin guard üzerinden oturum kontrolü yap
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        return redirect('/'); // Admin değilse giriş sayfasına yönlendir
    }
}

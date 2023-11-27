<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedireccionInicio
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * 
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userRole = auth()->user()->rol;

            if ($userRole == 1) {
                return redirect('/dashboard');
            } elseif ($userRole == 4) {
                return redirect('/docente');
            } elseif ($userRole == 5) {
                return redirect('/estudiante/inicio');
            } elseif ($userRole == 6) {
                return redirect('/supervisor');
            }elseif ($userRole == 0) {
                return redirect('/superadmi');
            }
        }
    
        return $next($request);

    }
}

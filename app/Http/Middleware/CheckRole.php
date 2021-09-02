<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if ($request->user()->hasRole($role)) {
            return $next($request);
        }
        $resp = new \stdClass();
        $resp->state = false;
        $resp->message = 'No tiene autorización para utilizar este servicio.';
        return response()->json($resp, 200);
    }
}

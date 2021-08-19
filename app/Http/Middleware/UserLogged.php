<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use function PHPSTORM_META\map;

class UserLogged
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
        if($request->user()->tokenCan('user:actions')) {
            return $next($request);
        } else {
            return response()->json([
                'success' => false,
                'status_code' => 403,
                'message' => 'User is not logged'
            ], 403);
        }
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check() && Auth::user()->accountstatus)
        {
            $banned = Auth::user()->accountstatus == "1"; // "1"= user is banned / "0"= user is unBanned
            Auth::logout();

            if ($banned == 1) {
                $message = 'Your account has been Blocked. Please contact Admin.';
            }
            return redirect()->route('login')
                ->with('error',$message);
              
            
        }

        return $next($request);
    }
}

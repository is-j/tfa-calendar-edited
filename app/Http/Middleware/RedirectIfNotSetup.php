<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotSetup
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
        $role = Auth::user()->role->name;
        if ($role != 'admin' && !DB::table($role . 's')->where('user_id', Auth::user()->id)->exists()) {
            return redirect('setup');
        }
        return $next($request);
    }
}

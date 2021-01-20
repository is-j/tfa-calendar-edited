<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckIfSetup
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
        $role = User::find(Auth::user()->id)->role();
        if (!DB::table($role . 's')->where('user_id', Auth::user()->id)->exists()) {
            return redirect()->route('setup');
        }
        return $next($request);
    }
}

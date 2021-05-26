<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Route;
use PragmaRX\Google2FA\Google2FA;

class Google2faEnable
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
        if (Auth::user()) {
            $user = Auth::user();
            if ($user->is_member == 1 || $user->is_admin == 1) {
                // If auth user was verified by Google 2FA
                if (session('is_google2fa_verified', false)) {
                    return $next($request);
                }
                if ($user['2fa_enabled'] == 0) {
                    return redirect()->route('show2faForm');
                }                    
                else {
                    return redirect()->route('show2faverify');
                }                    
            }
        }
        return $next($request);
    }
}

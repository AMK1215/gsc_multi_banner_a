<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class AdminLogoMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            $logoFilename = $user->agent_logo;
            if($user->hasRole('Agent') || $user->hasRole('Sub Agent')){
                $siteName = 'Agent Dashboard';
            }else{
                $siteName = $user->site_name ?? 'DelightMyanmar'; // Default site name
            }
            $adminLogo = $logoFilename
                ? asset('assets/img/logo/'.$logoFilename)
                : asset('assets/img/logo/default-logo.jpg');

            View::share([
                'adminLogo' => $adminLogo,
                'siteName' => $siteName, // Share site name globally
            ]);
        }

        return $next($request);
    }

}

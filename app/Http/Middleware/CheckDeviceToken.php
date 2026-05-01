<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDeviceToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $token = session('current_device_token');
            
            // If session has a token, check if it still exists in the DB
            if ($token) {
                $exists = \App\Models\UserDevice::where('user_id', auth()->id())
                    ->where('token', $token)
                    ->exists();

                if (!$exists) {
                    auth()->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect()->route('login')->with('error', 'Your session has been revoked from another device.');
                }
            }
        }

        return $next($request);
    }
}

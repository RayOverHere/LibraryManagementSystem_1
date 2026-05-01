<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = strtolower($request->login) . '|' . $request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'login' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->withInput();
        }

        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (auth()->attempt([$fieldType => $request->login, 'password' => $request->password], $request->boolean('remember'))) {
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Register/Update Device
            $deviceName = $request->userAgent() ?: 'Unknown Device';
            $ipAddress = $request->ip();
            
            // Check if this device already has a token for this IP and User Agent
            $device = auth()->user()->devices()->updateOrCreate(
                [
                    'device_name' => $deviceName,
                    'ip_address' => $ipAddress,
                ],
                [
                    'token' => \Illuminate\Support\Str::random(40),
                    'last_active_at' => now()
                ]
            );

            // Store this specific device token in session
            session(['current_device_token' => $device->token]);

            if (auth()->user()->role === 'admin') {
                return redirect()->intended('/admin/books');
            }

            return redirect()->intended('/dashboard');
        }

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey);

        return back()->withErrors([
            'login' => 'Wrong email or password.',
        ])->withInput();
    }

    public function destroy(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

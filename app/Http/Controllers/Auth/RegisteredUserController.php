<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // Concatenate phone number before validation
        $fullPhone = $request->country_code . ltrim($request->phone_number, '0');
        $request->merge(['phone' => $fullPhone]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'regex:/^[a-z0-9.]{6,30}@gmail\.com$/i'],
            'phone' => ['required', 'string', 'unique:users', 'regex:/^\+[1-9]\d{1,3}\d{8,12}$/'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers()->symbols()],
        ], [
            'email.regex' => 'Invalid Gmail. Use a-z, 0-9, dots, and 6-30 characters.',
            'phone.regex' => 'Invalid Phone format. Ensure you selected the correct country code and entered a valid number.',
            'phone.unique' => 'This phone number is already registered.'
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'member',
        ]);

        auth()->login($user);

        // Register Device
        $deviceName = $request->userAgent() ?: 'Unknown Device';
        $ipAddress = $request->ip();
        $token = \Illuminate\Support\Str::random(40);
        $device = $user->devices()->create([
            'device_name' => $deviceName,
            'ip_address' => $ipAddress,
            'token' => $token,
            'last_active_at' => now(),
        ]);
        session(['current_device_token' => $token]);

        return redirect('/dashboard');
    }
}

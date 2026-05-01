<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserDevice;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = auth()->user()->devices()->latest('last_active_at')->get();
        return view('user.devices.index', compact('devices'));
    }

    public function destroy(UserDevice $device)
    {
        // Ensure user owns the device
        if ($device->user_id !== auth()->id()) {
            abort(403);
        }

        // If revoking CURRENT device, log out
        $isCurrent = session('current_device_token') === $device->token;
        
        $device->delete();

        if ($isCurrent) {
            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect('/login')->with('success', 'Your current session has been revoked.');
        }

        return back()->with('success', 'Device token revoked successfully.');
    }
}

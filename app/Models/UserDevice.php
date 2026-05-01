<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $fillable = ['user_id', 'device_name', 'token', 'last_active_at', 'ip_address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getShortNameAttribute()
    {
        $ua = $this->device_name;
        $browser = "Unknown Browser";
        $os = "Unknown OS";

        // Browser Detection
        if (preg_match('/MSIE/i', $ua) && !preg_match('/Opera/i', $ua)) $browser = 'IE';
        elseif (preg_match('/Firefox/i', $ua)) $browser = 'Firefox';
        elseif (preg_match('/Chrome/i', $ua)) $browser = 'Chrome';
        elseif (preg_match('/Safari/i', $ua)) $browser = 'Safari';
        elseif (preg_match('/Opera/i', $ua)) $browser = 'Opera';
        elseif (preg_match('/Netscape/i', $ua)) $browser = 'Netscape';

        // OS Detection
        if (preg_match('/windows|win32/i', $ua)) $os = 'Windows';
        elseif (preg_match('/macintosh|mac os x/i', $ua)) $os = 'Mac';
        elseif (preg_match('/linux/i', $ua)) $os = 'Linux';
        elseif (preg_match('/android/i', $ua)) $os = 'Android';
        elseif (preg_match('/iphone/i', $ua)) $os = 'iPhone';

        return "{$browser} on {$os}";
    }

    protected function casts(): array
    {
        return [
            'last_active_at' => 'datetime',
        ];
    }
}

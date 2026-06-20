<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Guest extends Model
{
    /** @use HasFactory<\Database\Factories\GuestFactory> */
    protected $fillable = [
        'name',
        'phone',
        'token',
        'status',
        'rsvp_status',
    ];

    public function deviceSessions()
    {
        return $this->hasOne(DeviceSession::class);
    }

    public static function generateToken(){
        do{
            $token = Str::random(32);
        }while (self::where('token', $token)->exists());
        
        return $token;
    }
}

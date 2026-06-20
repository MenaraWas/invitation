<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSession extends Model
{
    /** @use HasFactory<\Database\Factories\DeviceSessionFactory> */
    protected $fillable = [
        'guest_id',
        'fingerprint_hash',
        'user_agent',
        'ip_address',
        'first_accessed_at',
        'last_accessed_at',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

}

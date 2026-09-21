<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationSetting extends Model
{
    protected $fillable = [
        'event_name',
        'content_url',
        'content_type',
        'event_date',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public static function current(): self
    {
        return self::first() ?? self::create([
            'event_name' => 'Pernikahan Kami',
            'content_type' => 'website',
        ]);
    }
}

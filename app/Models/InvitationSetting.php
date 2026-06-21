<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationSetting extends Model
{
    //
    protected $fillable = [
        'content_url',
        'content_type',
        'event_date',
    ];

    public static function current():self {
        return self::first() ?? self::create(['content_type' => 'website']);
    }
}

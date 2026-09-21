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
        'slug',
        'phone',
        'seat_count',
        'token',
        'status',
        'rsvp_status',
        'whatsapp_status',
    ];

    protected function casts(): array
    {
        return [
            'seat_count' => 'integer',
        ];
    }

    public function deviceSession()
    {
        return $this->hasOne(DeviceSession::class);
    }

    public static function generateToken(){
        do{
            $token = Str::random(32);
        }while (self::where('token', $token)->exists());
        
        return $token;
    }

    protected static function booted(){
        static::creating(function ($guest) {
        if (empty($guest->token)) {
            $guest->token = self::generateToken();
        }

        if (empty($guest->slug)) {
            $guest->slug = self::generateSlug($guest->name);
        }
    });
    }

    public static function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'tamu';
        $slug = $base;
        $counter = 2;

        while (self::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}

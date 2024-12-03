<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'sociable_id',
        'sociable_type',
        'platform',
        'name',
        'handle',
        'email',
        'profile_image_url',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function sociable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            if (self::where('sociable_id', $account->sociable_id)
                ->where('sociable_type', $account->sociable_type)
                ->where('platform', $account->platform)
                ->where('handle', $account->handle)
                ->exists()
            ) {
                throw new \Exception('This account already exists for the given platform.', 403);
            }
        });
    }
}

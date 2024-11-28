<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    protected $fillable = [
        'post_id',
        'media_type',
        'url',
        'dimensions',
        'file_size',
        'mime_type',
    ];

    protected $casts = [
        'dimensions' => 'array',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}

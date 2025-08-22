<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialConnection extends Model
{
    protected $guarded = []; // ← así es más simple
    protected $casts = [
        'expires_at' => 'datetime',
        'scopes'     => 'array',
        'meta'       => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];
    protected $casts = [
        'scheduled_for' => 'datetime',
        'published_at'  => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function targets() { return $this->hasMany(PostTarget::class); }

    // Scopes útiles
    public function scopeQueued($q)     { $q->where('status','queued'); }
    public function scopeScheduled($q)  { $q->where('status','scheduled'); }
    public function scopePendingLike($q){ $q->whereIn('status',['queued','scheduled']); }
}

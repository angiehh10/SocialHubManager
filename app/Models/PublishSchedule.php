<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublishSchedule extends Model
{
    protected $guarded = [];
    protected $casts = ['time' => 'datetime:H:i:s', 'active' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
}

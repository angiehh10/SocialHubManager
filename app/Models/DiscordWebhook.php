<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscordWebhook extends Model
{
    protected $fillable = [
        'user_id','social_connection_id','discord_webhook_id','token','url',
        'guild_id','channel_id','name','avatar',
    ];
}

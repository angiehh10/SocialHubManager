<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('discord_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('social_connection_id')->nullable()->constrained('social_connections')->nullOnDelete();
            $table->string('discord_webhook_id');      // id numérico del webhook en Discord
            $table->string('token');                   // token del webhook
            $table->string('url');                     // https://discord.com/api/webhooks/{id}/{token}
            $table->string('guild_id')->nullable();
            $table->string('channel_id')->nullable();
            $table->string('name')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();

            $table->unique(['user_id','discord_webhook_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discord_webhooks');
    }
};

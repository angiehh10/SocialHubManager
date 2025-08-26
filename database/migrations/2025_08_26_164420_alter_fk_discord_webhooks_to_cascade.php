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
        Schema::table('discord_webhooks', function (Blueprint $table) {
                  $table->dropForeign(['social_connection_id']);

            $table->foreign('social_connection_id')
                ->references('id')->on('social_connections')
                ->cascadeOnDelete(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discord_webhooks', function (Blueprint $table) {
             $table->dropForeign(['social_connection_id']);

            $table->foreign('social_connection_id')
                ->references('id')->on('social_connections')
                ->nullOnDelete();
        });
    }
};

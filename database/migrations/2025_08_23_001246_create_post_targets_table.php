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
            Schema::create('post_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();

            $table->string('provider'); // 'reddit' | 'discord'

            // Parámetros específicos por red:
            $table->string('reddit_subreddit')->nullable();
            $table->enum('reddit_kind', ['self','link'])->nullable(); // self=texto, link=URL

            $table->text('discord_webhook_url')->nullable();

            // Estado por destino (por si un destino falla y otro no)
            $table->enum('status', ['pending','sent','failed'])->default('pending');
            $table->string('external_id')->nullable();
            $table->text('error')->nullable();

            $table->timestamps();
            $table->index(['provider','status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_targets');
    }
};

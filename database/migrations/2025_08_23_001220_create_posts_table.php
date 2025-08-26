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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->text('title')->nullable();      // Reddit lo usa en link/text
            $table->longText('body')->nullable();   // texto cuerpo
            $table->string('link_url')->nullable(); // si publicas como link

            $table->enum('mode', ['now','queue','scheduled']);
            $table->dateTime('scheduled_for')->nullable();

            $table->enum('status', ['queued','scheduled','publishing','published','failed'])
                ->default('queued');
            $table->dateTime('published_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['user_id','status']);
            $table->index(['scheduled_for']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

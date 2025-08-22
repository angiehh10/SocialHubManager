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
        Schema::create('social_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('provider'); // facebook | linkedin | (otros)
            $table->string('provider_user_id')->nullable();

            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->json('scopes')->nullable();
            $table->json('meta')->nullable(); // info útil (ej: urn de LinkedIn, páginas FB)
            $table->timestamps();

            $table->unique(['user_id', 'provider']); // 1 conexión por proveedor/usuario
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_connections');
    }
};

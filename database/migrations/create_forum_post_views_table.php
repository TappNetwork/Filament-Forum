<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_post_views', function (Blueprint $table) {
            $table->id();

            // Add tenant relationship if tenancy is enabled
            if (config('filament-forum.tenancy.enabled')) {
                $tenantModel = config('filament-forum.tenancy.model');
                $table->foreignIdFor($tenantModel)
                    ->constrained()
                    ->cascadeOnDelete();
            }

            $table->foreignId('forum_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Ensure a user can only view a post once
            $table->unique(['forum_post_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_post_views');
    }
};

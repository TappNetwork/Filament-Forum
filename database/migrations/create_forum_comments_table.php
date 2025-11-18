<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_comments', function (Blueprint $table) {
            $table->id();

            // Add tenant relationship if tenancy is enabled
            if (config('filament-forum.tenancy.enabled')) {
                $tenantModel = config('filament-forum.tenancy.model');
                $table->foreignIdFor($tenantModel)
                    ->constrained()
                    ->cascadeOnDelete();
            }

            $table->foreignId('forum_post_id')->constrained()->cascadeOnDelete();
            $table->morphs('author');
            $table->longText('content');
            $table->timestamps();

            $table->index(['forum_post_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_comments');
    }
};

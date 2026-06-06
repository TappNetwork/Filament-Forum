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
        Schema::create('forum_post_subscriptions', function (Blueprint $table) {
            $table->id();
            $tenantColumn = null;

            if (config('filament-forum.tenancy.enabled')) {
                $tenantModel = config('filament-forum.tenancy.model');
                $tenantColumn = config('filament-forum.tenancy.column')
                    ?: str($tenantModel)->classBasename()->snake()->append('_id')->toString();

                $table->foreignIdFor($tenantModel)
                    ->constrained()
                    ->cascadeOnDelete();
            }

            $table->foreignId('forum_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $uniqueColumns = config('filament-forum.tenancy.enabled')
                ? [(string) $tenantColumn, 'forum_post_id', 'user_id']
                : ['forum_post_id', 'user_id'];

            $table->unique($uniqueColumns);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_post_subscriptions');
    }
};

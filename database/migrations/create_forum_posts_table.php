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
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();

            // Add tenant relationship if tenancy is enabled
            if (config('filament-forum.tenancy.enabled')) {
                $tenantModel = config('filament-forum.tenancy.model');
                $table->foreignIdFor($tenantModel)
                    ->constrained()
                    ->cascadeOnDelete();
            }

            $table->string('name');
            $table->text('description');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('forum_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
    }
};

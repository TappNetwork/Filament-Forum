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
        Schema::create('forums', function (Blueprint $table) {
            $table->id();

            // Add tenant relationship if tenancy is enabled
            if (config('filament-forum.tenancy.enabled')) {
                $tenantModel = config('filament-forum.tenancy.model');
                $table->foreignIdFor($tenantModel)
                    ->constrained()
                    ->cascadeOnDelete();
            }

            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forums');
    }
};

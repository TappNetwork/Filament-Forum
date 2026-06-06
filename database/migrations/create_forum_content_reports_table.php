<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_content_reports', function (Blueprint $table) {
            $table->id();

            if (config('filament-forum.tenancy.enabled')) {
                $tenantModel = config('filament-forum.tenancy.model');
                $table->foreignIdFor($tenantModel)
                    ->constrained()
                    ->cascadeOnDelete();
            }

            $table->morphs('reportable');
            $table->morphs('reporter');
            $table->string('reason')->nullable();
            $table->text('details')->nullable();
            $table->string('status')->default('pending')->index();
            $userModel = config('auth.providers.users.model');
            $table->foreignId('reviewed_by_id')->nullable()->constrained((new $userModel)->getTable())->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('resolution_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_content_reports');
    }
};

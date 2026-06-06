<?php

namespace Tapp\FilamentForum\Models\Traits;

use Filament\Facades\Filament;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tapp\FilamentForum\Events\ForumSubscribed;
use Tapp\FilamentForum\Events\ForumUnsubscribed;

trait CanSubscribeToForum
{
    public function toggleSubscription($user = null): void
    {
        $user ??= auth()->user();

        if (! $user) {
            return;
        }

        if ($this->isSubscribed($user)) {
            $this->subscribedUsers()->detach($user->getKey());

            ForumUnsubscribed::dispatch($user, $this);

            return;
        }

        $this->subscribedUsers()->attach($user->getKey(), $this->subscriptionPivotData());

        ForumSubscribed::dispatch($user, $this);
    }

    public function isSubscribed($user = null): bool
    {
        $user ??= auth()->user();

        if (! $user) {
            return false;
        }

        return $this->subscribedUsers()
            ->whereKey($user->getKey())
            ->when(
                config('filament-forum.tenancy.enabled') && Filament::hasTenancy(),
                fn (Builder $query) => $query->where(
                    'forum_subscriptions.'.static::getTenantColumnName(),
                    Filament::getTenant()->getKey(),
                ),
            )
            ->exists();
    }

    public function getIsSubscribedAttribute(): bool
    {
        return $this->isSubscribed();
    }

    public function subscribedUsers(): BelongsToMany
    {
        $relationship = $this->belongsToMany(config('auth.providers.users.model'), 'forum_subscriptions');

        if (config('filament-forum.tenancy.enabled')) {
            $relationship->withPivot(static::getTenantColumnName());
        }

        return $relationship->withTimestamps();
    }

    public function scopeSubscribed($query): Builder
    {
        return $query->whereHas('subscribedUsers', function ($query) {
            $query->where('forum_subscriptions.user_id', auth()->id());

            if (config('filament-forum.tenancy.enabled') && Filament::hasTenancy()) {
                $query->where('forum_subscriptions.'.static::getTenantColumnName(), Filament::getTenant()->getKey());
            }
        });
    }

    public function scopeNotSubscribed($query): Builder
    {
        return $query->whereDoesntHave('subscribedUsers', function ($query) {
            $query->where('forum_subscriptions.user_id', auth()->id());

            if (config('filament-forum.tenancy.enabled') && Filament::hasTenancy()) {
                $query->where('forum_subscriptions.'.static::getTenantColumnName(), Filament::getTenant()->getKey());
            }
        });
    }

    protected function subscriptionPivotData(): array
    {
        if (! config('filament-forum.tenancy.enabled') || ! Filament::hasTenancy()) {
            return [];
        }

        return [
            static::getTenantColumnName() => Filament::getTenant()->getKey(),
        ];
    }
}

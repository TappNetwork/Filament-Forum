<?php

namespace Tapp\FilamentForum\Models;

use Tapp\FilamentForum\Events\ForumPostCreated;
use Database\Factories\ForumPostFactory;
use Tapp\FilamentForum\Models\Traits\CanFavoriteForumPost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Kirschbaum\Commentions\HasComments;
use Kirschbaum\Commentions\Contracts\Commentable;
use Tapp\FilamentForum\Models\ForumPostView;
use App\Models\User;

class ForumPost extends Model implements Commentable
{
    use CanFavoriteForumPost;
    use HasComments;
    /** @use HasFactory<ForumPostFactory> */
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($forumPost) {
            ForumPostCreated::dispatch($forumPost);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ForumPostView::class);
    }

    public function recordView(): void
    {
        if (! Auth::check()) {
            return;
        }

        $this->views()->firstOrCreate([
            'user_id' => Auth::id(),
        ]);
    }

    public function getViewsCountAttribute(): int
    {
        return $this->views()->count();
    }

    public function hasBeenViewedByUser($user = null): bool
    {
        if (! $user && ! Auth::check()) {
            return false;
        }

        $userId = $user->id ?? Auth::id();

        return $this->views()
            ->where('user_id', $userId)
            ->exists();
    }

    public function getCountDistinctUsersWhoCommented()
    {
        return $this->comments()->distinct('author_id')->count();
    }

    public function getLastCommentTime(): ?string
    {
        $lastComment = $this->comments()
            ->latest()
            ->first();

        /** @phpstan-ignore-next-line */
        return $lastComment?->created_at?->diffForHumans();
    }

    public function getCommentAuthors(): Collection
    {
        return $this->comments()
            ->with('author')
            ->select(['author_id', 'author_type'])
            ->selectRaw('MAX(created_at) as last_comment')
            ->groupBy('author_id', 'author_type')
            ->orderByRaw('MAX(created_at) DESC')
            ->take(3)
            ->get()
            /** @phpstan-ignore-next-line */
            ->map(fn ($comment) => $comment->author);
    }
} 
<?php

namespace Tapp\FilamentForum\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Tapp\FilamentForum\Database\Factories\ForumPostFactory;
use Tapp\FilamentForum\Events\ForumPostCreated;
use Tapp\FilamentForum\Events\PostWasReacted;
use Tapp\FilamentForum\Models\Traits\BelongsToTenant;
use Tapp\FilamentForum\Models\Traits\CanFavoriteForumPost;

class ForumPost extends Model
{
    use BelongsToTenant;
    use CanFavoriteForumPost;

    /** @use HasFactory<ForumPostFactory> */
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return ForumPostFactory::new();
    }

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

    public function comments(): HasMany
    {
        return $this->hasMany(ForumComment::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(ForumPostReaction::class);
    }

    public function toggleReaction(string $reaction, $user): void
    {
        $existingReaction = $this->reactions()
            ->where('reactor_id', $user->getKey())
            ->where('reactor_type', get_class($user))
            ->where('type', $reaction)
            ->first();

        if ($existingReaction) {
            $existingReaction->delete();
        } else {
            $reaction = $this->reactions()->create([
                'reactor_id' => $user->getKey(),
                'reactor_type' => get_class($user),
                'type' => $reaction,
            ]);

            PostWasReacted::dispatch(
                $user,
                $this,
                $reaction
            );
        }
    }

    public function getReactionCounts(): array
    {
        return $this->reactions()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    public function getUserReactions($user): Collection
    {
        if (! $user) {
            return collect();
        }

        return $this->reactions()
            ->where('reactor_id', $user->getKey())
            ->where('reactor_type', get_class($user))
            ->pluck('type');
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

    public function commentsQuery(): HasMany
    {
        return $this->comments()
            ->latest()
            ->with(['author', 'reactions.reactor']);
    }

    public function getComments(): Collection
    {
        return $this->commentsQuery()->get();
    }

    public function addComment(string $content, $author): ForumComment
    {
        /** @var ForumComment */
        return $this->comments()->create([
            'content' => $content,
            'author_id' => $author->getKey(),
            'author_type' => get_class($author),
        ]);
    }

    public function getCountDistinctUsersWhoCommented(): int
    {
        return $this->comments()->distinct('author_id')->count();
    }

    public function getLastCommentTime(): ?string
    {
        $lastComment = $this->comments()
            ->latest()
            ->first();

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
            ->map(fn ($comment) => $comment->author);
    }

    public function hasBeenEdited(): bool
    {
        return $this->created_at->ne($this->updated_at);
    }
}

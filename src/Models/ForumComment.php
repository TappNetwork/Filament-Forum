<?php

namespace Tapp\FilamentForum\Models;

use Carbon\Carbon;
use DateTime;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tapp\FilamentForum\Events\CommentWasReacted;

/**
 * @property int $id
 * @property string $content
 * @property int $author_id
 * @property string $author_type
 * @property int $forum_post_id
 * @property Model $author
 * @property ForumPost $forumPost
 * @property-read DateTime|Carbon $created_at
 * @property-read DateTime|Carbon $updated_at
 */
class ForumComment extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'content',
        'author_type',
        'author_id',
        'forum_post_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function author(): MorphTo
    {
        return $this->morphTo();
    }

    public function forumPost(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(ForumCommentReaction::class);
    }

    public function isAuthor($author): bool
    {
        return $this->author_id === $author->getKey() && $this->author_type === get_class($author);
    }

    /**
     * Get the IDs of users mentioned in the comment content.
     */
    public function getMentioned(): Collection
    {
        $userModel = config('auth.providers.users.model');

        preg_match_all(
            '/<span[^>]*data-type="mention"[^>]*data-id="(\d+)"[^>]*>/',
            $this->content,
            $matches
        );

        if (empty($matches[1])) {
            return new \Illuminate\Database\Eloquent\Collection; // Return empty collection
        }

        $userIds = $matches[1];

        return $userModel::whereIn('id', $userIds)->get();
    }

    public function getAuthorName(): string
    {
        return $this->author->name ?? 'Unknown';
    }

    public function getAuthorAvatar(): string
    {
        $avatar = null;

        if ($this->author instanceof HasAvatar) {
            $avatar = $this->author->getFilamentAvatarUrl();
        }

        if (! is_null($avatar)) {
            return $avatar;
        }

        $name = str($this->getAuthorName())
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=FFFFFF&background=71717b';
    }

    public function hasBeenEdited(): bool
    {
        return $this->created_at->timestamp !== $this->updated_at->timestamp;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'text/plain']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);
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

            CommentWasReacted::dispatch(
                $user, 
                $this,
                $reaction
            );

            // Dispatch CommentWasReacted event for users mentioned in this comment
            // $mentioned = $this->getMentioned();
            // if ($mentioned->isNotEmpty()) {
            //     foreach ($mentioned as $user) {
            //         CommentWasReacted::dispatch(
            //             $user, 
            //             $this,
            //             $reaction
            //         );
            //     }
            // }
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
}

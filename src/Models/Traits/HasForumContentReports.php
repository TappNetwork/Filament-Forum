<?php

namespace Tapp\FilamentForum\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Tapp\FilamentForum\Models\ForumContentReport;

trait HasForumContentReports
{
    public function forumContentReports(): MorphMany
    {
        return $this->morphMany(ForumContentReport::class, 'reporter');
    }
}

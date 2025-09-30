<?php

namespace Tapp\FilamentForum\Concerns;

trait HasCustomForumPostBreadcrumb
{
    public function getBreadcrumbs(): array
    {
        $forumResource = config('filament-forum.resources.forumResource');
        $forumRecord = $this->getParentRecord();
        $operation = (string) str($this->form->getOperation());

        return [
            $forumResource::getUrl('index') => __('filament-forum::filament-forum.forum.breadcrumb'),
            $forumResource::getUrl('forum-posts', ['record' => $forumRecord]) => __('filament-forum::filament-forum.forum-post.breadcrumb'),
            '' => __('filament-forum::filament-forum.forum-post.'.$operation),
        ];
    }
}

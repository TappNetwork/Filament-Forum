<?php

namespace Tapp\FilamentForum\Concerns;

trait HasCustomForumPostBreadcrumb
{
    public function getBreadcrumbs(): array
    {
        $forumResource = config('filament-forum.resources.forumResource');
        $forumRecord = $this->getParentRecord();

        return [
            $forumResource::getUrl('index') => config('filament-forum.frontend.forum.breadcrumb'),
            $forumResource::getUrl('forum-posts', ['record' => $forumRecord]) => config('filament-forum.frontend.forum-posts.breadcrumb'),
            '' => str($this->form->getOperation())->ucfirst(),
        ];
    }
}

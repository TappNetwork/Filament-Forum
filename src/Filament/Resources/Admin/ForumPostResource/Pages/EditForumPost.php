<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource\Pages;

use Filament\Actions\DeleteAction;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;
use Tapp\FilamentForum\Models\ForumPost;

class EditForumPost extends EditRecord
{
    protected static string $resource = ForumPostResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        
        $forumPost = $this->getRecord();
        
        if (!$forumPost->canBeEditedBy()) {
            abort(403, 'You are not authorized to edit this forum post.');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CommentsAction::make(),
            DeleteAction::make(),
        ];
    }
} 
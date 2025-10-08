<?php

namespace Tapp\FilamentForum\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Tapp\FilamentForum\Events\ForumCommentCreated;
use Tapp\FilamentForum\Events\UserWasMentioned;
use Tapp\FilamentForum\Models\ForumComment;
use Tapp\FilamentForum\Models\ForumPost;

class ForumComments extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ForumPost $record;

    public ?array $data = [];

    public Collection $comments;

    public ?Collection $mentionables = null;

    public bool $paginated = false;

    public int $perPage = 10;

    public int $currentPage = 1;

    public ?string $pollingInterval = null;

    public ?int $editingCommentId = null;

    public ?array $editingData = [];

    public function mount(
        ForumPost $record,
        ?Collection $mentionables = null,
        bool $paginated = false,
        int $perPage = 10,
        ?string $pollingInterval = null
    ): void {
        $this->record = $record;
        $this->mentionables = $mentionables;
        $this->paginated = $paginated;
        $this->perPage = $perPage;
        $this->pollingInterval = $pollingInterval;

        $this->loadComments();
        $this->commentForm->fill();
    }

    public function commentForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('content')
                    ->label(__('filament-forum::filament-forum.comments.add-comment'))
                    ->placeholder(__('filament-forum::filament-forum.comments.placeholder'))
                    ->required()
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('forum-comments')
                    ->fileAttachmentsVisibility('public')
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function editCommentForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('content')
                    ->label(__('filament-forum::filament-forum.comments.edit-comment'))
                    ->placeholder(__('filament-forum::filament-forum.comments.placeholder'))
                    ->required()
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('forum-comments')
                    ->fileAttachmentsVisibility('public')
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('editingData');
    }

    public function create(): void
    {
        if (! Auth::check()) {
            Notification::make()
                ->title(__('filament-forum::filament-forum.comments.login-required'))
                ->danger()
                ->send();

            return;
        }

        // Use Filament's built-in schema validation
        $data = $this->commentForm->getState();

        // Process mentions in the content
        $processedContent = $this->processMentions($data['content']);

        $comment = $this->record->addComment(
            content: $processedContent,
            author: Auth::user()
        );

        // Handle mentions
        $mentioned = $this->extractMentions($processedContent);
        if ($mentioned->isNotEmpty()) {
            foreach ($mentioned as $user) {
                // Dispatch mention event
                UserWasMentioned::dispatch($user, $comment);
            }
        }

        // Dispatch comment created event
        ForumCommentCreated::dispatch($comment);

        // Reset form and reload comments
        $this->reset('data');
        $this->commentForm->fill();
        $this->loadComments();

        Notification::make()
            ->title(__('filament-forum::filament-forum.comments.created'))
            ->success()
            ->send();
    }

    #[On('comment-reaction-toggled')]
    public function refreshComments(): void
    {
        // Don't refresh anything - let each reaction component handle its own state
        // This prevents DOM manipulation errors
    }

    public function editComment(int $commentId): void
    {
        $comment = ForumComment::findOrFail($commentId);

        // Check if user can edit this comment
        if (! $this->canEditComment($comment)) {
            Notification::make()
                ->title(__('filament-forum::filament-forum.comments.unauthorized'))
                ->danger()
                ->send();

            return;
        }

        $this->editingCommentId = $commentId;
        $this->editingData = ['content' => $comment->content];
        $this->editCommentForm->fill($this->editingData);
    }

    public function updateComment(): void
    {
        if (! $this->editingCommentId) {
            return;
        }

        $comment = ForumComment::findOrFail($this->editingCommentId);

        // Check if user can edit this comment
        if (! $this->canEditComment($comment)) {
            Notification::make()
                ->title(__('filament-forum::filament-forum.comments.unauthorized'))
                ->danger()
                ->send();

            return;
        }

        $data = $this->editCommentForm->getState();

        // Process mentions in the content
        $processedContent = $this->processMentions($data['content']);

        $comment->update([
            'content' => $processedContent,
        ]);

        $this->cancelEdit();
        $this->loadComments();

        Notification::make()
            ->title(__('filament-forum::filament-forum.comments.updated'))
            ->success()
            ->send();
    }

    public function deleteComment(int $commentId): void
    {
        $comment = ForumComment::findOrFail($commentId);

        // Check if user can delete this comment
        if (! $this->canDeleteComment($comment)) {
            Notification::make()
                ->title(__('filament-forum::filament-forum.comments.unauthorized'))
                ->danger()
                ->send();

            return;
        }

        $comment->delete();

        // Use a more conservative approach - just remove from collection
        $this->comments = $this->comments->reject(function ($c) use ($commentId) {
            return $c->id === $commentId;
        });

        Notification::make()
            ->title(__('filament-forum::filament-forum.comments.deleted'))
            ->success()
            ->send();
    }

    public function deleteCommentAction(): Action
    {
        return Action::make('deleteComment')
            ->label(__('filament-forum::filament-forum.comments.delete'))
            ->icon('heroicon-o-trash')
            ->iconButton()
            ->color('gray')
            ->extraAttributes([
                'class' => 'p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors',
                'title' => __('filament-forum::filament-forum.comments.delete'),
            ])
            ->requiresConfirmation()
            ->modalHeading(__('filament-forum::filament-forum.comments.delete-modal-heading'))
            ->modalDescription(__('filament-forum::filament-forum.comments.delete-modal-description'))
            ->modalSubmitActionLabel(__('filament-forum::filament-forum.comments.delete'))
            ->modalSubmitAction(fn (Action $action) => $action->color('danger'))
            ->action(function (array $arguments) {
                $commentId = $arguments['commentId'];
                $comment = ForumComment::findOrFail($commentId);

                // Check if user can delete this comment
                if (! $this->canDeleteComment($comment)) {
                    Notification::make()
                        ->title(__('filament-forum::filament-forum.comments.unauthorized'))
                        ->danger()
                        ->send();

                    return;
                }

                $comment->delete();
                $this->loadComments();

                Notification::make()
                    ->title(__('filament-forum::filament-forum.comments.deleted'))
                    ->success()
                    ->send();
            });
    }

    public function cancelEdit(): void
    {
        $this->editingCommentId = null;
        $this->editingData = [];
    }

    protected function canEditComment(ForumComment $comment): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Users can edit their own comments
        return $comment->isAuthor(Auth::user());
    }

    protected function canDeleteComment(ForumComment $comment): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Users can delete their own comments
        return $comment->isAuthor(Auth::user());
    }

    public function loadMore(): void
    {
        if ($this->paginated) {
            $this->currentPage++;

            // Load only the new comments for this page
            $newComments = $this->record->comments()
                ->with(['author', 'reactions.reactor', 'media'])
                ->latest()
                ->skip(($this->currentPage - 1) * $this->perPage)
                ->take($this->perPage)
                ->get();

            // Append new comments to existing collection
            $this->comments = $this->comments->merge($newComments);
        }
    }

    protected function loadComments(): void
    {
        $query = $this->record->comments()
            ->with(['author', 'reactions.reactor', 'media'])
            ->latest();

        if ($this->paginated) {
            $this->comments = $query
                ->take($this->perPage * $this->currentPage)
                ->get();
        } else {
            $this->comments = $query->get();
        }
    }

    protected function extractMentions(string $content): \Illuminate\Database\Eloquent\Collection
    {
        $userModel = config('auth.providers.users.model');

        preg_match_all(
            '/<span[^>]*data-type="mention"[^>]*data-id="(\d+)"[^>]*>/',
            $content,
            $matches
        );

        if (empty($matches[1])) {
            return new \Illuminate\Database\Eloquent\Collection; // Return empty collection
        }

        $userIds = $matches[1];

        return $userModel::whereIn('id', $userIds)->get();
    }

    protected function processMentions(string $content): string
    {
        if (empty($content)) {
            return $content;
        }

        // Get all available users for mention lookup
        $mentionables = $this->getMentionablesData();
        $userLookup = collect($mentionables)->keyBy('name');

        // Find @username patterns in the content and replace with proper mention HTML
        // Simple approach: try to match each available username directly
        $processed = $content;

        // Sort usernames by length (longest first) to avoid partial matches
        $sortedUsernames = $userLookup->keys()->sortByDesc(function ($username) {
            return strlen($username);
        });

        foreach ($sortedUsernames as $username) {
            $pattern = '/@'.preg_quote($username, '/').'(?=\s|<|$)/u';
            $user = $userLookup->get($username);

            $replacement = sprintf(
                '<span class="mention" data-type="mention" data-id="%s" data-label="%s">@%s</span>',
                $user['id'],
                htmlspecialchars($username, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($username, ENT_QUOTES, 'UTF-8')
            );

            $newContent = preg_replace($pattern, $replacement, $processed);
            if ($newContent !== $processed) {
                $processed = $newContent;
            }
        }

        return $processed;
    }

    protected function getMentionablesData(): array
    {
        if ($this->mentionables) {
            return $this->mentionables->map(function ($user) {
                return [
                    'id' => $user->getKey(),
                    'name' => $user->name ?? 'Unknown User',
                    'avatar' => method_exists($user, 'getFilamentAvatarUrl')
                        ? $user->getFilamentAvatarUrl()
                        : null,
                ];
            })->toArray();
        }

        // Fallback to all users if no mentionables provided
        $userModel = config('auth.providers.users.model');

        return $userModel::limit(50)->get()->map(function ($user) {
            return [
                'id' => $user->getKey(),
                'name' => $user->name ?? 'Unknown User',
                'avatar' => method_exists($user, 'getFilamentAvatarUrl')
                    ? $user->getFilamentAvatarUrl()
                    : null,
            ];
        })->toArray();
    }

    public function renderRichContent(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        try {
            return RichContentRenderer::make($content)
                ->fileAttachmentsDisk('public')
                ->toHtml();
        } catch (\Exception $e) {
            // Fallback to raw HTML if RichContentRenderer fails
            return $content;
        }
    }

    public function render()
    {
        return view('filament-forum::livewire.forum-comments');
    }
}

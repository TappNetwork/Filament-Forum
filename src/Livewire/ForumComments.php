<?php

namespace Tapp\FilamentForum\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Tapp\FilamentForum\Events\ForumCommentCreated;
use Tapp\FilamentForum\Models\ForumPost;

class ForumComments extends Component implements HasSchemas, HasActions
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

        $comment = $this->record->addComment(
            content: $data['content'],
            author: Auth::user()
        );

        // Handle mentions
        $mentioned = $this->extractMentions(is_string($data['content']) ? $data['content'] : '');
        if ($mentioned->isNotEmpty()) {
            foreach ($mentioned as $user) {
                // Dispatch mention event if needed
                // UserWasMentionedEvent::dispatch($user, $comment);
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

    public function loadMore(): void
    {
        if ($this->paginated) {
            $this->currentPage++;
            $this->loadComments();
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
            return new \Illuminate\Database\Eloquent\Collection(); // Return empty collection
        }
        
        $userIds = $matches[1];
        
        return $userModel::whereIn('id', $userIds)->get();
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

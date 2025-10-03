<?php

namespace Tapp\FilamentForum\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Tapp\FilamentForum\Models\ForumComment;

class ForumCommentReactions extends Component
{
    public ForumComment $comment;

    public array $reactionCounts = [];

    public array $userReactions = [];

    public bool $showReactionPicker = false;

    public array $availableReactions = [
        '👍' => 'Like',
        '❤️' => 'Love', 
        '😂' => 'Laugh',
        '😮' => 'Wow',
        '😢' => 'Sad',
        '😡' => 'Angry',
    ];

    public function mount(ForumComment $comment): void
    {
        $this->comment = $comment;
        $this->loadReactions();
    }

    public function hydrate()
    {
        // Ensure reactions are loaded after hydration
        if (empty($this->reactionCounts)) {
            $this->loadReactions();
        }
    }

    public function toggleReaction(string $type): void
    {
        if (! Auth::check()) {
            return;
        }

        $this->comment->toggleReaction($type, Auth::user());
        $this->loadReactions();
        $this->showReactionPicker = false;

        // No need to dispatch events - each component manages its own state
    }

    public function toggleReactionPicker(): void
    {
        if (! Auth::check()) {
            return;
        }
        
        $this->showReactionPicker = ! $this->showReactionPicker;
    }

    protected function loadReactions(): void
    {
        try {
            $this->reactionCounts = $this->comment->getReactionCounts();
            $this->userReactions = Auth::check()
                ? $this->comment->getUserReactions(Auth::user())->toArray()
                : [];
        } catch (\Exception $e) {
            // Fallback to empty arrays if there's an issue
            $this->reactionCounts = [];
            $this->userReactions = [];
        }
    }

    public function render()
    {
        return view('filament-forum::livewire.forum-comment-reactions');
    }
}

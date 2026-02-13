<?php

namespace Tapp\FilamentForum\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Tapp\FilamentForum\Models\ForumPost;

class ForumPostReactions extends Component
{
    public ForumPost $post;

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

    public function mount(ForumPost $post): void
    {
        $this->post = $post;
        $this->loadReactions();
    }

    public function hydrate(): void
    {
        if (empty($this->reactionCounts)) {
            $this->loadReactions();
        }
    }

    public function toggleReaction(string $type): void
    {
        if (! Auth::check()) {
            return;
        }

        $this->post->toggleReaction($type, Auth::user());
        $this->loadReactions();
        $this->showReactionPicker = false;
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
            $this->reactionCounts = $this->post->getReactionCounts();
            $this->userReactions = Auth::check()
                ? $this->post->getUserReactions(Auth::user())->toArray()
                : [];
        } catch (\Exception $e) {
            $this->reactionCounts = [];
            $this->userReactions = [];
        }
    }

    public function render()
    {
        return view('filament-forum::livewire.forum-post-reactions');
    }
}

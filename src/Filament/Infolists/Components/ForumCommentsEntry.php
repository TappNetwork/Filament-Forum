<?php

namespace Tapp\FilamentForum\Filament\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Entry;
use Illuminate\Database\Eloquent\Collection;

class ForumCommentsEntry extends Entry
{
    protected string $view = 'filament-forum::filament.infolists.components.forum-comments-entry';

    protected array|Closure|null $mentionables = null;

    protected bool|Closure $paginated = false;

    protected int|Closure $perPage = 10;

    protected string|Closure|null $loadMoreLabel = null;

    protected int|Closure|null $perPageIncrement = null;

    protected string|Closure|null $pollingInterval = null;

    public function mentionables(array|Collection|Closure|null $mentionables): static
    {
        $this->mentionables = $mentionables;

        return $this;
    }

    public function getMentionables(): array|Collection|null
    {
        return $this->evaluate($this->mentionables);
    }

    public function paginated(bool|Closure $condition = true): static
    {
        $this->paginated = $condition;

        return $this;
    }

    public function isPaginated(): bool
    {
        return $this->evaluate($this->paginated);
    }

    public function perPage(int|Closure $perPage): static
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function getPerPage(): int
    {
        return $this->evaluate($this->perPage);
    }

    public function loadMoreLabel(string|Closure|null $label): static
    {
        $this->loadMoreLabel = $label;

        return $this;
    }

    public function getLoadMoreLabel(): ?string
    {
        return $this->evaluate($this->loadMoreLabel) ?? __('filament-forum::filament-forum.comments.load-more');
    }

    public function perPageIncrement(int|Closure|null $increment): static
    {
        $this->perPageIncrement = $increment;

        return $this;
    }

    public function getPerPageIncrement(): ?int
    {
        return $this->evaluate($this->perPageIncrement);
    }

    public function polling(string|Closure|null $interval = '5s'): static
    {
        $this->pollingInterval = $interval;

        return $this;
    }

    public function getPollingInterval(): ?string
    {
        return $this->evaluate($this->pollingInterval);
    }
}

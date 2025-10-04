<div class="space-y-6" @if($pollingInterval) wire:poll.{{ $pollingInterval }}="refreshComments" @endif>
    {{-- Comment Form --}}
    @auth
        <div class="bg-white dark:bg-gray-900 rounded-lg dark:border-gray-700"
             x-load
             x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('forum-mentions', 'tapp/filament-forum') }}"
             x-data="forumMentions({
                 mentionables: @js($this->getMentionablesData())
             })">
            <form wire:submit="create" class="space-y-4">
                {{ $this->commentForm }}
                
                <div class="flex justify-end">
                    <x-filament::button type="submit" wire:loading.attr="disabled">
                        <x-filament::loading-indicator wire:loading wire:target="create" class="h-4 w-4 mr-2" />
                        {{ __('filament-forum::filament-forum.comments.post-comment') }}
                    </x-filament::button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('filament-forum::filament-forum.comments.login-to-comment') }}
            </p>
        </div>
    @endauth

    {{-- Comments List --}}
    <div class="space-y-4" wire:key="comments-list">
        {{-- Comments Count --}}
        @if($comments->count() > 0)
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">
                    {{ trans_choice('filament-forum::filament-forum.comments.count', $comments->count(), ['count' => $comments->count()]) }}
                </h3>
            </div>
        @endif

        @forelse($comments as $comment)
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4" wire:key="comment-{{ $comment->id }}">
                {{-- Comment Header --}}
                <div class="flex items-start space-x-3 mb-3">
                    <img 
                        src="{{ $comment->getAuthorAvatar() }}" 
                        alt="{{ $comment->getAuthorName() }}"
                        class="w-8 h-8 rounded-full flex-shrink-0"
                    >
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $comment->getAuthorName() }}
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                                @if($comment->hasBeenEdited())
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        ({{ __('filament-forum::filament-forum.comments.edited') }})
                                    </span>
                                @endif
                            </div>
                            
                            {{-- Edit/Delete Actions --}}
                            @auth
                                @if($comment->isAuthor(Auth::user()))
                                    <div class="flex items-center space-x-1">
                                        @if($editingCommentId !== $comment->id)
                                            <button
                                                wire:click="editComment({{ $comment->id }})"
                                                class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                                title="{{ __('filament-forum::filament-forum.comments.edit') }}"
                                            >
                                                <x-heroicon-o-pencil class="w-4 h-4" />
                                            </button>
                                            
                                            {{ ($this->deleteCommentAction)(['commentId' => $comment->id]) }}
                                        @endif
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                {{-- Comment Content --}}
                @if($editingCommentId === $comment->id)
                    {{-- Edit Form --}}
                    <div class="mb-3"
                         x-load
                         x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('forum-mentions', 'tapp/filament-forum') }}"
                         x-data="forumMentions({
                             mentionables: @js($this->getMentionablesData())
                         })">
                        <form wire:submit="updateComment" class="space-y-3">
                            {{ $this->editCommentForm }}
                            
                            <div class="flex justify-end space-x-2">
                                <x-filament::button 
                                    type="button" 
                                    color="gray" 
                                    wire:click="cancelEdit"
                                >
                                    {{ __('filament-forum::filament-forum.comments.cancel') }}
                                </x-filament::button>
                                <x-filament::button type="submit" wire:loading.attr="disabled">
                                    <x-filament::loading-indicator wire:loading wire:target="updateComment" class="h-4 w-4 mr-2" />
                                    {{ __('filament-forum::filament-forum.comments.save') }}
                                </x-filament::button>
                            </div>
                        </form>
                    </div>
                @else
                    {{-- Display Content --}}
                    <div class="prose dark:prose-invert max-w-none mb-3">
                        {!! $this->renderRichContent($comment->content) !!}
                    </div>
                @endif

                {{-- Comment Attachments --}}
                @if($comment->hasMedia('attachments'))
                    <div class="mb-3">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                            @foreach($comment->getMedia('attachments') as $media)
                                @if(str_starts_with($media->mime_type, 'image/'))
                                    <div class="relative group">
                                        <img 
                                            src="{{ $media->getUrl('thumb') }}" 
                                            alt="{{ $media->name }}"
                                            class="w-full h-24 object-cover rounded cursor-pointer hover:opacity-90 transition-opacity"
                                            onclick="window.open('{{ $media->getUrl() }}', '_blank')"
                                        >
                                    </div>
                                @else
                                    <a 
                                        href="{{ $media->getUrl() }}" 
                                        target="_blank"
                                        class="flex items-center space-x-2 p-2 bg-gray-50 dark:bg-gray-800 rounded border hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                    >
                                        <x-heroicon-o-document class="w-4 h-4 text-gray-500" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate">
                                            {{ $media->name }}
                                        </span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Comment Reactions --}}
                @livewire('tapp.filament-forum.forum-comment-reactions', ['comment' => $comment], key('comment-reactions-' . $comment->id))
            </div>
        @empty
            <div class="text-center py-8">
                <x-heroicon-o-chat-bubble-left class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                <p class="text-gray-500 dark:text-gray-400">
                    {{ __('filament-forum::filament-forum.comments.no-comments') }}
                </p>
            </div>
        @endforelse

        {{-- Load More Button --}}
        @if($paginated && $comments->count() >= ($perPage * $currentPage))
            <div class="text-center">
                <x-filament::button 
                    wire:click="loadMore" 
                    variant="outlined"
                    wire:loading.attr="disabled"
                >
                    <x-filament::loading-indicator wire:loading wire:target="loadMore" class="h-4 w-4 mr-2" />
                    {{ __('filament-forum::filament-forum.comments.load-more') }}
                </x-filament::button>
            </div>
        @endif
    </div>

    {{-- Action Modals --}}
    <x-filament-actions::modals />
</div>

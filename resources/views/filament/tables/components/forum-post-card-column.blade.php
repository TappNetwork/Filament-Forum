@php
    use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource;
    $record = $getRecord();
    $isFavorite = $record->isFavorite();
@endphp

<div class="w-full bg-white rounded-xl shadow-xs overflow-hidden">
    <div class="p-4">
        {{-- Header with user avatar and post info --}}
        <div class="flex items-start justify-between">
            <a href="{{ ForumPostResource::getUrl('view', ['forum' => $record->forum->id, 'record' => $record->id]) }}">
                <div class="flex items-start space-x-4">
                    <img
                        src="{{ $record->user->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($record->user->name ?? __('filament-forum::filament-forum.forum-post.unknown')) }}"
                        alt="{{ $record->user->name ?? __('filament-forum::filament-forum.forum-post.unknown') }}"
                        class="w-10 h-10 rounded-full"
                    >
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $record->name }}
                            @if($record->hasBeenEdited())
                                <span class="text-sm text-gray-500 font-normal pl-2">{{ __('filament-forum::filament-forum.forum-post.edited') }}</span>
                            @endif
                        </h3>
                        <p class="text-sm text-gray-500">
                            @if($record->getLastCommentTime())
                            {{ __('filament-forum::filament-forum.forum-post.last-reply') }} {{ $record->getLastCommentTime() }}
                            @else
                            {{ __('filament-forum::filament-forum.forum-post.no-replies') }}
                            @endif
                        </p>
                    </div>
                </div>
            </a>

            {{-- Favorite Toggle --}}
            <div x-cloak x-data="{ isFavorite: {{ $isFavorite ? 'true' : 'false' }} }"
                 x-on:favorite-toggled.window="if ($event.detail.recordId === {{ $record->getKey() }}) isFavorite = $event.detail.isFavorite">
                <button
                    wire:click="toggleFavorite({{ $record->getKey() }})"
                    type="button"
                    :class="{
                        'transition-colors': true
                    }"
                >
                    <div x-tooltip.raw="{{ __('filament-forum::filament-forum.forum-post.toggle-favorite') }}">
                        <x-filament::loading-indicator
                            wire:loading
                            wire:target="toggleFavorite({{ $record->getKey() }})"
                            class="w-6 h-6 text-primary-500"
                        />

                        <div wire:loading.remove>
                            <x-heroicon-s-star class="w-6 h-6 text-primary-500" x-show="isFavorite" />
                            <x-heroicon-o-star class="w-6 h-6 text-gray-400 hover:text-primary-500" x-show="!isFavorite" />
                        </div>
                    </div>
                </button>
            </div>
        </div>

        {{-- Description --}}
        <a href="{{ ForumPostResource::getUrl('view', ['forum' => $record->forum->id, 'record' => $record->id]) }}">
            <div class="mt-4">
                <p class="text-sm text-gray-600">
                    {{ Str::limit($record->description, 200) }}
                </p>
            </div>
        </a>

        {{-- Footer with stats, reactions and comment authors --}}
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
            <div class="text-sm text-gray-500">
                @php
                    $viewsCount = $record->views_count ?? 0;
                    $replies = $record->comments()->count();
                @endphp

                {{ trans_choice('filament-forum::filament-forum.forum-post.views', $viewsCount, ['value' => $viewsCount]) }} &bull; {{ trans_choice('filament-forum::filament-forum.forum-post.replies', $replies, ['value' => $replies]) }}
            </div>

            <div class="flex items-center gap-3">
                {{-- Post Reactions --}}
                <div>
                    @livewire('tapp.filament-forum.forum-post-reactions', ['post' => $record], key('post-reactions-' . $record->id))
                </div>

                {{-- Comment Authors --}}
                <div class="flex items-center -space-x-2">
                    @foreach($record->getCommentAuthors() as $author)
                        <img
                            src="{{ $author->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($author->name) }}"
                            alt="{{ $author->name }}"
                            class="w-8 h-8 rounded-full border-2 border-white"
                        >
                    @endforeach
                    @if($record->getCountDistinctUsersWhoCommented() > 3)
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 border-2 border-white">
                            <span class="text-xs text-gray-500 font-medium">
                                +{{ $record->getCountDistinctUsersWhoCommented() - 3 }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

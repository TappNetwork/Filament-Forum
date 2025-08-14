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
                        src="{{ $record->user->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($record->user->name ?? 'Unknown') }}"
                        alt="{{ $record->user->name ?? 'Unknown' }}"
                        class="w-10 h-10 rounded-full"
                    >
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $record->name }}
                            @if($record->hasBeenEdited())
                                <span class="text-sm text-gray-500 font-normal pl-2">edited</span>
                            @endif
                        </h3>
                        <p class="text-sm text-gray-500">
                            @if($record->getLastCommentTime())
                                Last reply {{ $record->getLastCommentTime() }}
                            @else
                                No replies yet
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
                    <div x-tooltip.raw="Toggle Favorite">
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

        {{-- Footer with stats and comment authors --}}
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
            <div class="text-sm text-gray-500">
                {{ $record->views_count ?? 0 }} views &bull; {{ $record->comments()->count() }} replies
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

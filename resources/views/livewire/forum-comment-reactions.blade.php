<div class="flex items-center justify-between">
    {{-- Existing Reactions Display --}}
    <div class="flex items-center space-x-1">
        @foreach($reactionCounts as $type => $count)
            @if($count > 0)
                <button
                    wire:click="toggleReaction('{{ $type }}')"
                    @class([
                        'flex items-center space-x-1 px-2 py-1 rounded-full text-sm transition-colors border',
                        'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-700' => in_array($type, $userReactions),
                        'hover:bg-gray-100 text-gray-600 border-gray-200 dark:hover:bg-gray-700 dark:text-gray-400 dark:border-gray-600' => !in_array($type, $userReactions),
                    ])
                    @auth
                        title="{{ $availableReactions[$type] ?? $type }}"
                    @else
                        disabled
                        title="{{ __('filament-forum::filament-forum.comments.login-to-react') }}"
                    @endauth
                >
                    <span>{{ $type }}</span>
                    <span class="text-xs font-medium">{{ $count }}</span>
                </button>
            @endif
        @endforeach
    </div>

    {{-- Add Reaction Button --}}
    <div class="relative" x-data="{ open: @entangle('showReactionPicker') }" x-on:click.outside="open = false">
        @auth
            <button
                wire:click="toggleReactionPicker"
                class="flex items-center space-x-1 px-2 py-1 rounded-full text-sm transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400"
                title="{{ __('filament-forum::filament-forum.comments.add-reaction') }}"
            >
                <span class="text-lg">😊</span>
                <span class="text-xs">{{ __('filament-forum::filament-forum.comments.add-reaction') }}</span>
            </button>

            {{-- Reaction Picker --}}
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute bottom-full left-0 mb-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg p-2 z-10">
                <div class="flex space-x-1">
                    @foreach($availableReactions as $emoji => $label)
                        <button
                            wire:click="toggleReaction('{{ $emoji }}')"
                            class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            title="{{ $label }}"
                        >
                            <span class="text-lg">{{ $emoji }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        @else
            <button
                disabled
                class="flex items-center space-x-1 px-2 py-1 rounded-full text-sm text-gray-400 cursor-not-allowed"
                title="{{ __('filament-forum::filament-forum.comments.login-to-react') }}"
            >
                <span class="text-lg">😊</span>
                <span class="text-xs">{{ __('filament-forum::filament-forum.comments.add-reaction') }}</span>
            </button>
        @endauth
    </div>
</div>

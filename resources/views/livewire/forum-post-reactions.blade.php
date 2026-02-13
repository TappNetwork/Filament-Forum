<div class="flex items-center gap-1">
    {{-- Existing Reactions Display (clickable to toggle) --}}
    @foreach($reactionCounts as $type => $count)
        @if($count > 0)
            <button
                wire:click="toggleReaction('{{ $type }}')"
                @class([
                    'flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-sm transition-colors border',
                    'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-700' => in_array($type, $userReactions),
                    'hover:bg-gray-100 text-gray-600 border-gray-200 dark:hover:bg-gray-700 dark:text-gray-400 dark:border-gray-600' => !in_array($type, $userReactions),
                ])
                @auth
                    x-tooltip.raw="{{ $availableReactions[$type] ?? $type }}"
                @else
                    disabled
                    x-tooltip.raw="{{ __('filament-forum::filament-forum.comments.login-to-react') }}"
                @endauth
            >
                <span>{{ $type }}</span>
                <span class="text-xs font-medium">{{ $count }}</span>
            </button>
        @endif
    @endforeach

    {{-- Smiley icon to open reaction picker --}}
    <div class="relative" x-data="{ open: @entangle('showReactionPicker') }" x-on:click.outside="open = false">
        @auth
            <button
                wire:click="toggleReactionPicker"
                class="flex items-center justify-center w-7 h-7 rounded-full transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
                x-tooltip.raw="{{ __('filament-forum::filament-forum.comments.add-reaction') }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                </svg>
            </button>

            {{-- Reaction Picker --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute bottom-full right-0 mb-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg p-2 z-10">
                <div class="flex space-x-1">
                    @foreach($availableReactions as $emoji => $label)
                        <button
                            wire:click="toggleReaction('{{ $emoji }}')"
                            class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            x-tooltip.raw="{{ $label }}"
                        >
                            <span class="text-lg">{{ $emoji }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        @else
            <button
                disabled
                class="flex items-center justify-center w-7 h-7 rounded-full text-gray-300 cursor-not-allowed dark:text-gray-600"
                x-tooltip.raw="{{ __('filament-forum::filament-forum.comments.login-to-react') }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                </svg>
            </button>
        @endauth
    </div>
</div>

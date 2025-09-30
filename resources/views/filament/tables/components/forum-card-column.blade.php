@php
    use Tapp\FilamentForum\Filament\Resources\Forums\ForumResource;
    $record = $getRecord();
@endphp

<div class="w-full h-full flex flex-col">
    {{-- Header with image --}}
    <div class="relative h-28">
        @if($image = $record->getMedia('images')->first())
            <img
                src="{{ $image->getUrl() }}"
                alt="{{ $record->name }}"
                class="w-full h-full object-cover"
            >
        @endif
    </div>

    {{-- Content --}}
    <div class="pt-6">
        <h3 class="text-xl font-semibold text-gray-900">{{ $record->name }}</h3>
        <p class="text-sm text-gray-500 mt-1">{{ __('filament-forum::filament-forum.forum.active') }} {{ $record->getLastActivity() }}</p>

        <p class="text-sm text-gray-600 mt-4 grow">
            {{ Str::limit($record->description, 150) }}
        </p>
    </div>

    <div class="pt-6">
        {{-- Footer --}}
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
            <div class="flex -space-x-2">
                @foreach($record->getRecentUsers() as $user)
                    <img
                        src="{{ $user->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7C3AED&background=EBF4FF' }}"
                        alt="{{ $user->name }}"
                        class="w-8 h-8 rounded-full border-2 border-white"
                    >
                @endforeach
                @if($record->getCountDistinctUsersWhoPosted() > 3)
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 border-2 border-white">
                        <span class="text-xs text-gray-500 font-medium">
                            +{{ $record->getCountDistinctUsersWhoPosted() - 3 }}
                        </span>
                    </div>
                @endif
            </div>

            <x-filament::button
                class="bg-primary-600 hover:bg-primary-700 text-white"
                href="{{ ForumResource::getUrl('forum-posts', ['record' => $record->id]) }}"
                tag="a"
            >
                {{ __('filament-forum::filament-forum.forum.view-posts') }}
            </x-filament::button>
        </div>
    </div>
</div>

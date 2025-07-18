@php
    use Tapp\FilamentForum\Filament\Resources\ForumPosts\ForumPostResource;
    $record = $getRecord();
@endphp

<div class="w-full h-full flex flex-col">
    {{-- Content --}}
    <div class="pt-6">
        <h3 class="text-xl font-semibold text-gray-900">{{ $record->name }}</h3>
        <p class="text-sm text-gray-500 mt-1">
            By {{ $record->user->name ?? 'Unknown' }} • {{ $record->created_at->diffForHumans() }}
        </p>

        <p class="text-sm text-gray-600 mt-4 grow">
            {{ Str::limit($record->description, 150) }}
        </p>
    </div>

    <div class="pt-6">
        {{-- Footer --}}
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
            <div class="flex items-center space-x-2">
                @if($record->isFavorite())
                    <span class="text-yellow-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </span>
                @endif
                <span class="text-sm text-gray-500">
                    {{ $record->views_count ?? 0 }} views
                </span>
            </div>

            <x-filament::button
                class="bg-primary-600 hover:bg-primary-700 text-white"
                href="{{ ForumPostResource::getUrl('view', ['record' => $record->id, 'forum' => $record->forum->id]) }}"
                tag="a"
            >
                View Post
            </x-filament::button>
        </div>
    </div>
</div> 
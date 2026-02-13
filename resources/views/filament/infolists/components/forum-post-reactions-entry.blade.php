@php
    $record = $getRecord();
@endphp

<div class="fi-in-entry-wrp">
    @livewire('tapp.filament-forum.forum-post-reactions', [
        'post' => $record,
    ], key('post-reactions-' . $record->id))
</div>

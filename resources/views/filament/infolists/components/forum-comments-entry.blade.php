@php
    $record = $getRecord();
    $mentionables = $getMentionables();
    $paginated = $isPaginated();
    $perPage = $getPerPage();
    $pollingInterval = $getPollingInterval();
@endphp

<div class="fi-in-entry-wrp">
    @livewire('tapp.filament-forum.forum-comments', [
        'record' => $record,
        'mentionables' => $mentionables,
        'paginated' => $paginated,
        'perPage' => $perPage,
        'pollingInterval' => $pollingInterval,
    ])
</div>

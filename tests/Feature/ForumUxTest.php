<?php

it('renders comment count with readable text color in blade template', function () {
    $bladePath = __DIR__.'/../../resources/views/livewire/forum-comments.blade.php';

    $contents = file_get_contents($bladePath);

    expect($contents)->toContain('text-gray-900 dark:text-gray-100');
});

it('renders post card with stretched link markup', function () {
    $bladePath = __DIR__.'/../../resources/views/filament/tables/components/forum-post-card-column.blade.php';

    $contents = file_get_contents($bladePath);

    expect($contents)
        ->toContain('forum-post-card')
        ->toContain('forum-post-card__link')
        ->toContain('forum-post-card__control');
});

it('includes stretched link styles in built css', function () {
    $cssPath = __DIR__.'/../../resources/dist/filament-forum.css';

    $contents = file_get_contents($cssPath);

    expect($contents)
        ->toContain('.forum-post-card {')
        ->toContain('.forum-post-card__link {')
        ->toContain('.forum-post-card__control {');
});

<?php

namespace Tapp\FilamentForum\Filament\RichEditor\Plugins;

use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;

class MentionPlugin implements RichContentPlugin
{
    public function getId(): string
    {
        return 'mention';
    }

    /**
     * @return array<object>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [];
    }

    public function getTipTapJsExtensions(): array
    {
        // Return empty array - we'll handle mentions through our custom JavaScript
        // without requiring external TipTap extensions
        return [];
    }

    public function getEditorTools(): array
    {
        return [];
    }

    public function getEditorActions(): array
    {
        return [];
    }
}

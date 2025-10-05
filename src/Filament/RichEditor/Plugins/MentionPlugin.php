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
        return [
            new \Tapp\FilamentForum\TipTap\Extensions\Mention,
        ];
    }

    public function getTipTapJsExtensions(): array
    {
        // Return empty - we handle mentions with server-side processing
        // The PHP extension is sufficient for parsing existing content
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

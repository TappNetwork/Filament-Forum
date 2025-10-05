<?php

namespace Tapp\FilamentForum\TipTap\Extensions;

use Tiptap\Core\Node;

class Mention extends Node
{
    public static $name = 'mention';

    public function addAttributes(): array
    {
        return [
            'id' => [
                'default' => null,
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-id'),
                'renderHTML' => fn ($attributes) => [
                    'data-id' => $attributes->id ?? null,
                ],
            ],
            'label' => [
                'default' => null,
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-label'),
                'renderHTML' => fn ($attributes) => [
                    'data-label' => $attributes->label ?? null,
                ],
            ],
            'type' => [
                'default' => 'mention',
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-type'),
                'renderHTML' => fn ($attributes) => [
                    'data-type' => $attributes->type ?? 'mention',
                ],
            ],
        ];
    }

    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'span[data-type="mention"]',
            ],
        ];
    }

    public function renderHTML($node, $HTMLAttributes = []): array
    {
        return [
            'span',
            array_merge([
                'class' => 'mention',
                'data-type' => 'mention',
            ], $HTMLAttributes),
            '@' . ($HTMLAttributes['data-label'] ?? ''),
        ];
    }
}

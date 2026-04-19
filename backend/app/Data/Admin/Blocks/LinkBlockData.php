<?php

declare(strict_types=1);

namespace App\Data\Admin\Blocks;

use App\Enums\BlockAlign;
use App\Enums\BlockType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class LinkBlockData extends BlockData
{
    public function __construct(
        public readonly string $label,
        public readonly string $href,
        public readonly BlockAlign $align = BlockAlign::Left,
    ) {
        parent::__construct(BlockType::Link);
    }

    /** @return array<string, array<int, string>> */
    public static function validationRules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'href' => ['required', 'url'],
            'align' => ['sometimes', 'string', 'in:left,center,right'],
        ];
    }

    /** @return array<string, array<int, string>> */
    public static function previewRules(): array
    {
        return [
            'label' => ['sometimes', 'string', 'max:255'],
            'href' => ['sometimes', 'string', 'max:2048'],
            'align' => ['sometimes', 'string', 'in:left,center,right'],
        ];
    }
}

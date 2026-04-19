<?php

declare(strict_types=1);

namespace App\Data\Admin\Blocks;

use App\Enums\BlockAlign;
use App\Enums\BlockType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TextBlockData extends BlockData
{
    public function __construct(
        public readonly string $body,
        public readonly BlockAlign $align = BlockAlign::Left,
    ) {
        parent::__construct(BlockType::Text);
    }

    /** @return array<string, array<int, string>> */
    public static function validationRules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
            'align' => ['sometimes', 'string', 'in:left,center,right'],
        ];
    }

    /** @return array<string, array<int, string>> */
    public static function previewRules(): array
    {
        return [
            'body' => ['sometimes', 'string', 'max:5000'],
            'align' => ['sometimes', 'string', 'in:left,center,right'],
        ];
    }
}

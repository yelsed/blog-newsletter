<?php

declare(strict_types=1);

namespace App\Data\Admin\Blocks;

use App\Enums\BlockAlign;
use App\Enums\BlockType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ButtonBlockData extends BlockData
{
    public function __construct(
        public readonly string $label,
        public readonly string $href,
        public readonly BlockAlign $align = BlockAlign::Left,
    ) {
        parent::__construct(BlockType::Button);
    }

    /** @return array<string, array<int, string>> */
    public static function validationRules(): array
    {
        return [
            'label' => ['required', 'string', 'max:80'],
            'href' => ['required', 'url', 'max:2048'],
            'align' => ['sometimes', 'string', 'in:left,center,right'],
        ];
    }

    /** @return array<string, array<int, string>> */
    public static function previewRules(): array
    {
        return [
            'label' => ['sometimes', 'string', 'max:80'],
            'href' => ['sometimes', 'string', 'max:2048'],
            'align' => ['sometimes', 'string', 'in:left,center,right'],
        ];
    }
}

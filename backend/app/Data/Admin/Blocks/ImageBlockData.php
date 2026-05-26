<?php

declare(strict_types=1);

namespace App\Data\Admin\Blocks;

use App\Enums\BlockType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ImageBlockData extends BlockData
{
    public function __construct(
        public readonly string $path,
        public readonly string $alt,
        public readonly ?int $width = null,
        public readonly ?string $href = null,
    ) {
        parent::__construct(BlockType::Image);
    }

    /** @return array<string, array<int, string>> */
    public static function validationRules(): array
    {
        return [
            'path' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_\-.\/]+$/', 'not_regex:/\.\./'],
            'alt' => ['required', 'string', 'max:255'],
            'width' => ['nullable', 'integer', 'min:1', 'max:2000'],
            'href' => ['nullable', 'url', 'max:2048'],
        ];
    }

    /** @return array<string, array<int, string>> */
    public static function previewRules(): array
    {
        return [
            'path' => ['sometimes', 'string', 'max:255'],
            'alt' => ['sometimes', 'string', 'max:255'],
            'width' => ['nullable', 'integer', 'min:1', 'max:2000'],
            'href' => ['nullable', 'string', 'max:2048'],
        ];
    }
}

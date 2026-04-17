<?php

declare(strict_types=1);

namespace App\Data\Admin\Blocks;

use App\Enums\BlockType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ImageBlockData extends BlockData
{
    public function __construct(
        public readonly string $url,
        public readonly string $alt,
        public readonly ?int $width = null,
        public readonly ?string $href = null,
    ) {
        parent::__construct(BlockType::Image);
    }

    /** @return array<string, array<int, string>> */
    public static function rules(): array
    {
        return [
            'url' => ['required', 'url', 'max:2048'],
            'alt' => ['required', 'string', 'max:255'],
            'width' => ['nullable', 'integer', 'min:1', 'max:2000'],
            'href' => ['nullable', 'url', 'max:2048'],
        ];
    }
}

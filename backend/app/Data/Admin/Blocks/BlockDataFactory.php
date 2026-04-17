<?php

declare(strict_types=1);

namespace App\Data\Admin\Blocks;

use App\Enums\BlockType;
use InvalidArgumentException;

final class BlockDataFactory
{
    /** @param array<string, mixed> $payload */
    public static function fromArray(array $payload): BlockData
    {
        $type = BlockType::tryFrom((string) ($payload['type'] ?? ''));

        if ($type === null) {
            throw new InvalidArgumentException(
                'Unknown block type: '.var_export($payload['type'] ?? null, true)
            );
        }

        return match ($type) {
            BlockType::Text => TextBlockData::from($payload),
            BlockType::Link => LinkBlockData::from($payload),
            BlockType::ListItems => ListBlockData::from($payload),
            BlockType::Image => ImageBlockData::from($payload),
            BlockType::Gif => GifBlockData::from($payload),
            BlockType::Button => ButtonBlockData::from($payload),
        };
    }

    /**
     * Per-type validation rules, used by FormRequests to validate blocks.
     *
     * @return array<string, array<int, string>>
     */
    public static function rulesFor(BlockType $type): array
    {
        return match ($type) {
            BlockType::Text => TextBlockData::rules(),
            BlockType::Link => LinkBlockData::rules(),
            BlockType::ListItems => ListBlockData::rules(),
            BlockType::Image => ImageBlockData::rules(),
            BlockType::Gif => GifBlockData::rules(),
            BlockType::Button => ButtonBlockData::rules(),
        };
    }
}

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
            BlockType::Text => TextBlockData::validationRules(),
            BlockType::Link => LinkBlockData::validationRules(),
            BlockType::ListItems => ListBlockData::validationRules(),
            BlockType::Image => ImageBlockData::validationRules(),
            BlockType::Gif => GifBlockData::validationRules(),
            BlockType::Button => ButtonBlockData::validationRules(),
        };
    }

    /**
     * Lenient rules used when rendering a live preview while the composer is being filled in.
     *
     * @return array<string, array<int, string>>
     */
    public static function previewRulesFor(BlockType $type): array
    {
        return match ($type) {
            BlockType::Text => TextBlockData::previewRules(),
            BlockType::Link => LinkBlockData::previewRules(),
            BlockType::ListItems => ListBlockData::previewRules(),
            BlockType::Image => ImageBlockData::previewRules(),
            BlockType::Gif => GifBlockData::previewRules(),
            BlockType::Button => ButtonBlockData::previewRules(),
        };
    }
}

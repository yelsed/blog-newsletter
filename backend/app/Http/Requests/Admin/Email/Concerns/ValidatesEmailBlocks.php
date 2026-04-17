<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Email\Concerns;

use App\Data\Admin\Blocks\BlockDataFactory;
use App\Enums\BlockType;
use Illuminate\Support\Arr;

trait ValidatesEmailBlocks
{
    /** @return array<string, array<int, string>> */
    protected function emailBlockRules(): array
    {
        $types = implode(',', array_column(BlockType::cases(), 'value'));

        $rules = [
            'subject' => ['required', 'string', 'max:255'],
            'blocks' => ['required', 'array', 'min:1'],
            'blocks.*' => ['required', 'array'],
            'blocks.*.type' => ['required', 'string', 'in:'.$types],
        ];

        /** @var array<int, mixed> $blocks */
        $blocks = Arr::wrap($this->input('blocks', []));

        foreach ($blocks as $index => $block) {
            if (! is_array($block)) {
                continue;
            }

            $type = BlockType::tryFrom((string) ($block['type'] ?? ''));

            if ($type === null) {
                continue;
            }

            foreach (BlockDataFactory::rulesFor($type) as $field => $fieldRules) {
                $rules["blocks.{$index}.{$field}"] = $fieldRules;
            }
        }

        return $rules;
    }
}

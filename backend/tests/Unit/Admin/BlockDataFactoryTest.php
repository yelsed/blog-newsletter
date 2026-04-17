<?php

declare(strict_types=1);

use App\Data\Admin\Blocks\BlockDataFactory;
use App\Data\Admin\Blocks\ButtonBlockData;
use App\Data\Admin\Blocks\GifBlockData;
use App\Data\Admin\Blocks\ImageBlockData;
use App\Data\Admin\Blocks\LinkBlockData;
use App\Data\Admin\Blocks\ListBlockData;
use App\Data\Admin\Blocks\TextBlockData;
use App\Enums\BlockAlign;
use App\Enums\BlockType;

it('hydrates a text block', function () {
    $block = BlockDataFactory::fromArray([
        'type' => 'text',
        'body' => 'Hello world',
        'align' => 'center',
    ]);

    expect($block)->toBeInstanceOf(TextBlockData::class)
        ->and($block->type)->toBe(BlockType::Text)
        ->and($block->body)->toBe('Hello world')
        ->and($block->align)->toBe(BlockAlign::Center);
});

it('hydrates a link block', function () {
    $block = BlockDataFactory::fromArray([
        'type' => 'link',
        'label' => 'Click me',
        'href' => 'https://example.com',
    ]);

    expect($block)->toBeInstanceOf(LinkBlockData::class)
        ->and($block->href)->toBe('https://example.com');
});

it('hydrates a list block', function () {
    $block = BlockDataFactory::fromArray([
        'type' => 'list',
        'items' => ['one', 'two'],
        'ordered' => true,
    ]);

    expect($block)->toBeInstanceOf(ListBlockData::class)
        ->and($block->items)->toBe(['one', 'two'])
        ->and($block->ordered)->toBeTrue();
});

it('hydrates an image block', function () {
    $block = BlockDataFactory::fromArray([
        'type' => 'image',
        'url' => 'https://example.com/a.png',
        'alt' => 'pic',
    ]);

    expect($block)->toBeInstanceOf(ImageBlockData::class);
});

it('hydrates a gif block', function () {
    $block = BlockDataFactory::fromArray([
        'type' => 'gif',
        'url' => 'https://example.com/a.gif',
        'alt' => 'anim',
    ]);

    expect($block)->toBeInstanceOf(GifBlockData::class);
});

it('hydrates a button block', function () {
    $block = BlockDataFactory::fromArray([
        'type' => 'button',
        'label' => 'Go',
        'href' => 'https://example.com',
    ]);

    expect($block)->toBeInstanceOf(ButtonBlockData::class);
});

it('throws on an unknown block type', function () {
    BlockDataFactory::fromArray(['type' => 'quiz']);
})->throws(InvalidArgumentException::class);

it('exposes rules for each block type', function () {
    foreach (BlockType::cases() as $type) {
        expect(BlockDataFactory::rulesFor($type))->toBeArray()->not->toBeEmpty();
    }
});

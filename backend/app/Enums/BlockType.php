<?php

declare(strict_types=1);

namespace App\Enums;

enum BlockType: string
{
    case Text = 'text';
    case Link = 'link';
    case ListItems = 'list';
    case Image = 'image';
    case Gif = 'gif';
    case Button = 'button';
}

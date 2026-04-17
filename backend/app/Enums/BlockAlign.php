<?php

declare(strict_types=1);

namespace App\Enums;

enum BlockAlign: string
{
    case Left = 'left';
    case Center = 'center';
    case Right = 'right';
}

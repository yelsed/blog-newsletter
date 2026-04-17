<?php

declare(strict_types=1);

namespace App\Enums;

enum EmailStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
}

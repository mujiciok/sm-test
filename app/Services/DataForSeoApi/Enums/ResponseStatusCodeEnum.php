<?php

declare(strict_types=1);

namespace App\Services\DataForSeoApi\Enums;

enum ResponseStatusCodeEnum: int
{
    case OK = 20000;
    case TASK_CREATED = 20100;
    case INVALID_FIELD = 40501;
}

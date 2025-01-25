<?php

declare(strict_types=1);

namespace App\Dtos;

class KeywordApiDataDto
{
    public function __construct(
        public string $keyword,
        public int $searchVolume,
        public ?int $googleSerp,
    ) {
    }
}

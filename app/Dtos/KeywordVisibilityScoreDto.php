<?php

declare(strict_types=1);

namespace App\Dtos;

/**
 * @param string $keyword
 * @param int $searchVolume
 * @param int|null $googleSerp
 * @param float $visibilityScore
 */
class KeywordVisibilityScoreDto
{
    public function __construct(
        public string $keyword,
        public int $searchVolume,
        public ?int $googleSerp,
        public float $visibilityScore,
    ) {
    }
}
